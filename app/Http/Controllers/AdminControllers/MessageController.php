<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Masking;
use Auth;
use App\Imports\BulkSmsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\MessageMasking;
use App\Exports\MessageExport;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;
use App\Jobs\AdminSendBulkSms;
use Throwable;

class MessageController extends Controller
{
    public $pagination; 
    public function __construct()
    {
        $this->pagination = 10;
    }

    public function index()
    {
        $data['messages'] = Message::where('admin_id', Auth::id())->paginate($this->pagination);
        return view('admin.messages.index', $data);
    }

    public function messageCampaign()
    {
        $data['campaign'] = Campaign::where('admin_id', Auth::id())->paginate($this->pagination);
        return view('admin.campaign.index', $data);
    }

    public function create()
    {
        $data['maskings']  = Masking::get();
        return view('admin.messages.create', $data);
    }

    public function exportExcel(Request $request)
    {
        $data = [];
        $data['startDate']  = $request->start_date;
        $data['endDate']    = $request->end_date;
        return (new MessageExport($data))->download('messages.xlsx');
    }

    public function AuthSmsCount($messageLength)
    {
        $total_sms = Auth::user()->has_sms - $messageLength;
       return Auth::user()->update(['has_sms' => $total_sms]);
    }

    public function store(Request $request)
    {
        if(Auth::user()->has_sms == 0){
            return redirect()->back()->withErrors('You have zero balance');
        }
        $request->validate([
            'type'    => 'required|string',
        ]);

        $dataValidate = [
            'message' => 'required',
        ];

        if($request->type == 'single'){
            $dataValidate['phone_number'] = 'required|min:10|max:12';
        }else{
            $dataValidate['campaign'] = 'required';
            $dataValidate['file']     = 'file|required|mimes:xlsx,xls,xml';
        }

        if($request->late_shedule == 'on'){
            $dataValidate['sheduledatetime'] = 'required';
        }

        if(Auth::user()->type == 'masking'){
            $dataValidate['masking']  =  'required';
        }
        if(!isset($request->no_of_sms) && $request->no_of_sms == NULL){
            return redirect()->back()->withErrors('Something wents wrong with sms length');
        }
        
        $request->validate($dataValidate);

        $noOfSms  = $request->no_of_sms??1;
        
        if(Auth::user()->has_sms < $noOfSms){
            return redirect()->back()->withErrors('User have enough sms');
        }elseif($noOfSms > 5){
            return redirect()->back()->withErrors('Message maximum limit is 5');
        }
        
        $authType = 'masking';
        $data = [
            'admin_id'        => Auth::id(),
            'message'        => $request->message,
            'message_length' => $noOfSms,
            'contact_number' => $request->phone_number??NULL,
            // 'price'          => Auth::user()->price_per_sms * $noOfSms,
            'send_date'      => $request->sheduledatetime??Carbon::now(),
            'api_type'       => $authType,
            'campaign'       => $request->campaign,
        ];
        
        $data['status']      = ($request->late_shedule == NULL)? 'successfully' : 'pending';
        $mask = Masking::find($request->masking_id);
        $data['orginator'] = $mask->title;
        $data['masking_id'] =  $mask->id;
      
        switch ($request->type) {
            case 'single':
                $num = (substr($request->phone_number, 0, 2) == '03')? true : ((substr($request->phone_number, 0, 3) == '923')? true : ((substr($request->phone_number, 0, 1) == "3")? true:false) );
                if($num == false){
                    return redirect()->back()->withErrors('Plesae Start your number with 92, 03, 3');
                }
                $data['type'] = 'single';
                $htiApi = $this->hitApi($data);
                    if(isset($htiApi['Data']['msgid']) && !empty($htiApi['Data']['msgid'])){
                        if(substr($number, 0, 3) == '033' || substr($number, 0, 2) == '33' || substr($number, 0, 4) == '9233'){
                            $noOfSms = $noOfSms * 1.5;
                        }              
                        $data['message_id'] = $htiApi['Data']['msgid'];
                        $data['status']     = 'successfully';
                        $data['price']      = 1 * $noOfSms;
                        $sendMessage        = $this->saveMessage($data);
                        $dataResponse       = 'Message send successfully';
                    }else{
                        return redirect()->back()->withErrors($htiApi['Data']);
                    }
                break;
            case 'bulk':
                if($request->hasFile('file') && $request->file){
                    $filesexel = $this->readExportFile($request->file);
                    if($filesexel == false){
                        return redirect()->back()->withErrors('Something wents wrong with excel formate..! try again');
                    }else{
                        $data['type'] = 'campaign';
                        // $data['file'] = $request->file;
                        $campaign =  $this->save_campaign($data, $request->file,'pending');
                        $data['campaign_id'] = $campaign->id;
                        $data['url'] = $this->message_url($data);
                        $job = (new AdminSendBulkSms($data, $filesexel))->delay(now()->addSeconds(1));
                        $dataResponse       = 'Campaign run successfully';
                        dispatch($job);
                      
                    }
                }else{
                    return redirect()->back()->withErrors('Something wents wrong with your excel file! plesae contact your admistrator');
                }
            break;            
        }
        
        return redirect()->back()->with('success', $dataResponse);
    }

    public function message_url($data){   
        $url      = Auth::user()->adminApi->api_url;
        $username = Auth::user()->adminApi->api_username;
        $password = Auth::user()->adminApi->api_password;
        $url .= 'user='.$username;
        $url .= '&pwd='.$password;
        $url .= '&sender='.urlencode($data['orginator']);
        $url .= '&reciever='.$data['contact_number'];
        $url .= '&msg-data='.urlencode($data['message']);
        $url .= '&response=json';
        
        return $url;
    }
 

    public function save_campaign($data, $file, $status)
    {
        $file_path = $file->store('public/testing/');
        $file_name = $file->getClientOriginalName();
        $size = Storage::size($file_path);
        $data = [
            'user_id' => Auth::id(),
            'name' => $data['campaign'],
            'file_url' => $file_path,
            'file_name' => $file_name,
            'size' => $size,
            'campaign_date' => $data['send_date'],
            'status'    => $status,
        ];
       return $campaign =  Campaign::create($data);
    }

    public function hitApi($data)
    {
        $url = $this->message_url($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response =  curl_exec($ch);

        return json_decode($response, true);
    }

    public function campaignFileDownload($id)
    {
        $campaign = Campaign::findOrFail(decrypt($id));
        return  Storage::download($campaign->file_url);
    }

    public function readExportFile($file)
    {
        $numbers = [];
        $readExcel = Excel::toArray(new BulkSmsImport, $file);
        foreach ($readExcel[0] as $_read) {
            if(isset($_read['number'])){
                array_push($numbers, $_read['number']);
            }else{
                return false;
            }
        }
        return $numbers;
    }

    public function saveMessage($data, $campId = NULL){
        $this->AuthSmsCount($data['message_length']);
        $message = Message::create([
            'message_id'        => $data['message_id'],
            'admin_id'           => Auth::id(),
            'message'           => $data['message'],
            'message_length'    => $data['message_length'],
            'contact_number'    => $data['contact_number'],
            'send_date'         => $data['send_date'],
            'type'              => $data['type'],
            'price'             => $data['price'],
            'api_type'          => $data['api_type'],
            'status'            => $data['status'],
            'reference'         => $data['orginator']
        ]);

        if($data['api_type'] == 'masking'){
            MessageMasking::create([
                'message_id' => $message->id,
                'masking_id' => $data['masking_id']
            ]);
        }
        if($campId != NULL){
            CampaignMessage::create(['message_id' => $message->id, 'campaign_id' => $campId]);
        }
        return $message;
    }

    public function stringCount($message){
        $count = '';
        if (strlen($message) != strlen(utf8_decode($message))){
            $urduCount = strlen(utf8_decode($message));
            switch ($urduCount) {
                case $urduCount <= 70:
                        $count = 1;
                    break;
                case $urduCount <= 134:
                        $count = 2;
                    break;
                case $urduCount <= 201:
                        $count = 3;
                    break;  
                case $urduCount <= 268:
                        $count = 4;
                    break;  
                case $urduCount <= 355:
                        $count = 5;
                    break;  
                default:
                    $count = false;
                    break;
            }

        }else{
            $englishCount  = strlen($message);
            switch ($englishCount) {
                case $englishCount <= 160:
                        $count = 1;
                    break;
                case $englishCount <= 320:
                        $count = 2;
                    break;
                case $englishCount <= 480:
                        $count = 3;
                    break;  
                case $englishCount <= 640:
                        $count = 4;
                    break;  
                case $englishCount <= 800:
                        $count = 5;
                    break;  
                default:
                    $count = false;
                    break;
            }
        }

        return $count;
    }

}
