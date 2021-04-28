<?php

namespace App\Http\Controllers\DashboardControllers;

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
use App\Models\Admin;
use App\Jobs\SendBulkSms;

class MessageController extends Controller
{
    public $pagination; 
    public function __construct()
    {
        $this->pagination = 10;
    }

    public function index()
    {
        $data['messages'] = Message::where('user_id', Auth::id())->paginate($this->pagination);
        return view('dashboard.messages.index', $data);
    }

    public function messageCampaign()
    {
        $data['campaign'] = Campaign::where('user_id', Auth::id())->paginate($this->pagination);
        return view('dashboard.campaign.index', $data);
    }

    public function create()
    {
        $data['maskings']  = Auth::user()->getResellerMasking ;
        return view('dashboard.messages.create', $data);
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
        $total_sms = Auth::user()->UserData->has_sms - $messageLength;
        return Auth::user()->UserData()->update(['has_sms' => $total_sms]);
    }

    public function store(Request $request)
    {
        if(Auth::user()->UserData->has_sms == 0){
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
        
        $request->validate($dataValidate);

        $noOfSms  = $this->stringCount($request->message);
       



        if(Auth::user()->UserData->has_sms < $noOfSms){
            return redirect()->back()->withErrors('User have enough sms');
        }elseif($noOfSms > 5){
            return redirect()->back()->withErrors('Message maximum limit is 5');
        }
        
        $authType = Auth::user()->type;
        $data = [
            'user_id'        => Auth::id(),
            'message'        => $request->message,
            'message_length' => $noOfSms,
            'contact_number' => $request->phone_number??NULL,
            'price'          => Auth::user()->UserData->price_per_sms * $noOfSms,
            'send_date'      => $request->sheduledatetime??Carbon::now(),
            'api_type'       => $authType,
            'campaign'       => $request->campaign,
        ];
        
        $data['status']      = ($request->late_shedule == NULL)? 'successfully' : 'pending';
        if($authType == 'masking'){
            $mask = Masking::find($request->masking);
            $data['orginator'] = $mask->title;
            $data['masking_id'] = $mask->id;
        }else{
            $data['orginator'] = '99095';
        }
        switch ($request->type) {
            case 'single':
                $num = (substr($request->phone_number, 0, 2) == '03')? true : ((substr($request->phone_number, 0, 3) == '923')? true : ((substr($request->phone_number, 0, 1) == "3")? true:false) );
                if($num == false){
                    return redirect()->back()->withErrors('Plesae Start your number with 92, 03, 3');
                }
                $data['type'] = 'single';
                $htiApi = $this->hitApi($data);
                    if(Auth::user()->type == 'masking'){
                        if(isset($htiApi['Data']['msgid']) && !empty($htiApi['Data']['msgid'])){
                            if(substr($request->phone_number, 0, 3) == '033'){
                                $noOfSms += $noOfSms / 2 + $noOfSms;
                             }
                            $data['price']      = Auth::user()->UserData->price_per_sms * $noOfSms;
                            $data['message_id'] = $htiApi['Data']['msgid'];
                            $data['status']     = 'successfully';
                            $sendMessage        = $this->saveMessage($data);
                            $dataResponse       = 'Message send successfully';
                        }else{
                            return redirect()->back()->withErrors($htiApi['Data']);
                        }
                    }else{
                        if(isset($htiApi['data']) && isset($htiApi['data']['acceptreport']['messageid']) && $htiApi['action'] == "sendmessage"){
                            $data['message_id'] = $htiApi['data']['acceptreport']['messageid'];
                            $data['price']      = Auth::user()->UserData->price_per_sms * $noOfSms;
                            $data['status'] = 'successfully';
                            $sendMessage        = $this->saveMessage($data);
                            $dataResponse       = 'Message send successfully';
                        }else if(isset($htiApi['action']) && $htiApi['action'] == "error"){
                            return redirect()->back()->withErrors($htiApi['data']['errormessage']);
                        }else{
                            return redirect()->back()->withErrors('Something wents wrong! plesae contact your admistrator');
                        }
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
                            $campaign =  $this->save_campaign($data,$request->file,'pending');
                            $data['campaign_id'] = $campaign->id;
                            $data['url'] = $this->message_url($data);
                            $job = (new SendBulkSms($data, $filesexel))->delay(now()->addSeconds(1));
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
        $admin = Admin::first();
        $url      = Auth::user()->getUserSmsApi['api_url']??$admin->adminApi->api_url;
        $username = Auth::user()->getUserSmsApi['api_username']??$admin->adminApi->api_username;
        $password = Auth::user()->getUserSmsApi['api_password']??$admin->adminApi->api_password;
        if(Auth::user()->type == 'masking'){
            $url .= 'user='.$username;
            $url .= '&pwd='.$password;
            $url .= '&sender='.urlencode($data['orginator']);
            $url .= '&reciever='.$data['contact_number'];
            $url .= '&msg-data='.urlencode($data['message']);
            $url .= '&response=json';
        }else{
            $url .= 'action=sendmessage';
            $url .= '&username='.Auth::user()->getUserSmsApi->api_username;
            $url .= '&password='.Auth::user()->getUserSmsApi->api_password;
            $url .= '&recipient='.$data['contact_number'];
            $url .= '&originator='.$data['orginator'];
            $url .= '&messagedata='.urlencode($data['message']);
            $url .= '&sendondate='.urlencode(date('Y-m-d h:m:s', strtotime($data['send_date'])));
            $url .= '&responseformat=xml';
        }
        return $url;
    }

 

    public function save_campaign($data, $file, $status)
    {
        $data['file'] = $file;
        $file_path = $data['file']->store(Auth::user()->getBulkSmsExcelPath());
        $file_name = $data['file']->getClientOriginalName();
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

        if(Auth::user()->type == 'masking'){
            return json_decode($response, true);
        }
        $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        return $array = json_decode($json,TRUE);
        
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
            'user_id'           => Auth::id(),
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
