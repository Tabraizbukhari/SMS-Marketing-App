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

class MessageController extends Controller
{
    public $pagination; 
    public $api_url;
    public function __construct()
    {
        $this->pagination = 10;
        $this->api_url = 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?';
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
        $data['maskings']  =(Auth::user()->type == 'admin')? Masking::get() : Auth::user()->getResellerMasking ;
        return view('dashboard.messages.create', $data);
    }

    public function exportExcel(Request $request)
    {
        $data = [];
        $data['startDate']  = $request->start_date;
        $data['endDate']    = $request->end_date;
        return (new MessageExport($data))->download('messages.xlsx');
        // $messages   = Message::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->get();
        // return Excel::download(new MessageExport, 'messages.xlsx');
    }

    public function AuthSmsCount($messageLength)
    {
        $total_sms = Auth::user()->sms - $messageLength;
       return Auth::user()->update(['sms' => $total_sms]);
    }

    public function store(Request $request)
    {
        if(Auth::user()->sms == 0){
            return redirect()->back()->withErrors('User have zero sms');
        }
        
        $request->validate([
            'message'       => 'required',
            'no_of_sms'     => 'required',
            'phone_number'  => 'sometimes|required',
            'file'          => 'sometimes|required|file',
            'type'          => 'required|string',
            'phone_number'  => 'sometimes|required',
            'masking'       => 'sometimes|required',
            'campaign'      => 'sometimes|required',
            'file'          => 'sometimes|file|required|mimes:xlsx,xls,xml',
            'sheduledatetime' => 'sometimes|required', 
        ]);

        $masking_id = $request->masking_id??NULL;
        $messageLength = $request->no_of_sms??1;
        $data = [
            'user_id'        => Auth::id(),
            'message'        => $request->message,
            'message_length' => $messageLength,
            'contact_number' => ($request->has('phone_number'))? $request->phone_number : NULL,
            'send_date'      => ($request->has('sheduledatetime') && !empty($request->sheduledatetime))? $request->sheduledatetime : Carbon::now(),
            'type'           => $request->type,
            'campaign'       => $request->campaign,
        ];
        if($messageLength > 5){
            return redirect()->back()->withErrors('Message maximum limit is 5');
        }else{
            $data['price'] = $messageLength * Auth::user()->price;
        }

        if($masking_id){
            $data['masking_name'] = Masking::find($request->masking_id)->title;
            $data['api_type'] = 'masking';
        }else{
            $data['api_type'] = 'code';
        }
        if($request->hasFile('file') && $request->file){
            $filesexel = $this->readExportFile($request->file);
            if($filesexel == false){
                return redirect()->back()->withErrors("The excel file numbers column heading is not found, Something went wrong! Try again.");
            }
            $data['numbers'] = $filesexel;
            $data['file']    = $request->file;
        }
            if($$this->readExportFile($request->file))
        if($request->has('late_shedule') && $request->has('sheduledatetime') && $request->sheduledatetime != null){
            $data['status'] = 'pending';
            $data['campaign_status'] = 'pending';
            
            if($data['type'] == 'single'){
                $this->AuthSmsCount($messageLength);
                $this->saveMessage($data, $masking_id);
            }else{
                foreach ($data['numbers'] as $number) {
                    if(Auth::user()->sms == 0){
                        return redirect()->back()->withErrors('User have zero sms');
                    }
                    
                    $data['contact_number'] = $number;
                    $number_length = strlen($data['contact_number']);
                    if($number_length < 10 || $number_length > 11  ){
                        $data['status'] = 'not_sent';
                        $data['price']  = 0;
                    }else{
                        $this->AuthSmsCount($messageLength);
                        $data['status'] = 'pending';
                        $data['price']  = $messageLength * Auth::user()->price;
                    }
                    $this->saveMessage($data, $masking_id);
                }
                $this->save_campaign($data);
            }

        }else{
            $data['status'] = 'successfully';
            $data['campaign_status'] = 'completed';
            if($request->type == 'single'){
                $hitapi = $this->hitApi($data);
                if($hitapi == 'success'){
                    $this->AuthSmsCount($messageLength);
                    $this->saveMessage($data, $masking_id);
                }else{
                    $message = $hitapi??'Message Sending Failed';
                    return redirect()->back()->withErrors($message);
                }     
            }else{
                foreach ($data['numbers'] as $number) {
                    $data['contact_number'] = $number;
                    $number_length = strlen($data['contact_number']);
    
                    if(Auth::user()->sms == 0){
                        return redirect()->back()->withErrors('User have zero sms');
                    }

                    if($number_length < 10 || $number_length > 11  ){
                        $data['status'] = 'not_sent';
                        $data['price']  = 0;
                    }else{
                        $this->AuthSmsCount($messageLength);
                        $data['status'] = 'successfully';
                        $data['price']  = $messageLength * Auth::user()->price;
                    }

                    

                    $hitapi = $this->hitApi($data);
                    if($hitapi == 'success'){
                        $this->saveMessage($data, $masking_id);
                    }else{
                        $message = $hitapi??'Message Sending Failed';
                        return redirect()->back()->withErrors($message);
                    }
                }
                $this->save_campaign($data);
            }
        } 
        
       
        return redirect()->back()->with('success','Message Sending Successfully!');
    }

    public function message_url($data)
    {
        $url = Auth::user()->getUserSmsApi->api_url;
        if(Auth::user()->getUserSmsAPi->type == 'masking'){
            $url .= 'user='.Auth::user()->getUserSmsApi->api_username;
            $url .= '&pwd='.Auth::user()->getUserSmsApi->api_password;
            $url .= '&sender='.$data['masking_name'];
            $url .= '&reciever='.$data['contact_number'];
            $url .= '&msg-data='.$data['message'];
            $url .= '&response=json';
        }else{
            $url .= 'action=sendmessage';
            $url .= '&username='.Auth::user()->getUserSmsApi->api_username;
            $url .= '&password='.Auth::user()->getUserSmsApi->api_password;
            $url .= '&recipient='.$data['contact_number'];
            $url .= '&originator=99095';
            $url .= '&messagedata='.urlencode($data['message']);
            $url .= '&responseformat=html';
        }
        return $url;
    }

 

    public function save_campaign($data)
    {
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
            'status'    => $data['campaign_status'],
        ];
       return $campaign =  Campaign::create($data);
    }

    public function hitApi($data)
    {
        $url = $this->message_url($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result=  curl_exec($ch);
        $error = (isset($result))? json_decode($result): null;
        if(isset($error) && $error != null){
            if(!isset($error->Data->status)){
                return $error->Data;
            }
        }
        if($result == true){
            return 'success';
        }else{
            return $result;
        }
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
        foreach ($readExcel as $_read) {
            if(count($_read) > 0){
                foreach ($_read as $r) {
                    if(!isset($r['number'])){
                        return false;
                    }else{
                        $number = (int)$r['number'];
                        array_push($numbers, $number);
                    }
                }
            }
        }
        return $numbers;
    }

    public function saveMessage($data, $masking_id = null)
    {
        $message = Message::create([
            'user_id'           => $data['user_id'],
            'message'           => $data['message'],
            'message_length'    => $data['message_length'],
            'contact_number'    => $data['contact_number'],
            'send_date'         => $data['send_date'],
            'type'              => ($data['type'] == 'bulk')? 'campaign': $data['type'],
            'price'             => $data['price'],
            'api_type'          => $data['api_type'],
            'status'            => $data['status'],
        ]);
        switch ($data['api_type']) {
            case 'masking':
                    MessageMasking::create([
                        'message_id' => $message->id,
                        'masking_id' => $masking_id
                    ]);
                break;
            
            default:
                 return 'code';
                break;
        }
    }

}
