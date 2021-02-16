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

    public function store(Request $request)
    {
        $request->validate([
            'message'       => 'required',
            'no_of_sms'     => 'required',
            'phone_number'  => 'sometimes|required',
            'file'          => 'sometimes|required|file',
            'type'          => 'required|string',
            'phone_number'  => 'sometimes|required',
        ]);

        $masking_id = $request->masking_id??NULL;

        if(Auth::user()->sms == 0){
            return redirect()->back()->withErrors('User have zero sms');
        }
        
        $data = [
            'user_id'        => Auth::id(),
            'message'        => $request->message,
            'message_length' => $request->no_of_sms,
            'contact_number' => ($request->has('phone_number'))? $request->phone_number : NULL,
            'send_date'      => ($request->has('sheduledatetime') && !empty($request->sheduledatetime))? $request->sheduledatetime : Carbon::now(),
            'price'          => Auth::user()->price,
        ];
        $data['masking_name'] = Masking::find($request->masking_id)->title??NULL;
        $data['api_type'] = ($data['masking_name'] == null)? 'code' : 'masking';


        if($request->has('late_shedule') && $request->has('sheduledatetime') && $request->sheduledatetime != null){
            if($request->type == 'single'){
                $data['status'] = 'pending';
                $data['type'] = 'single';
                $sumSms = Auth::user()->sms - 1;
                Auth::user()->update(['sms' => $sumSms ]);
                $messageCreate = Message::create($data);
                if($request->has('masking_id') && $request->masking_id){
                    MessageMasking::create([
                        'message_id' => $messageCreate->id,
                        'masking_id' => $request->masking_id,
                    ]);
                }
            }else{
                $data['campaign'] = $request->campaign;
                $data['file'] = $request->file;
                $data['campaign_status'] = 'pending';
                $readExcel = Excel::toArray(new BulkSmsImport, $request->file);
                $c = $this->save_campaign($data);
                foreach ($readExcel as $_read) {
                    if(count($_read) > 0){
                        $sum = Auth::user()->sms - count($_read);
                        foreach ($_read as $r) {
                            $data['contact_number']= (int)$r['number'];
                            $hitapi = $this->hitApi($data);
                            if($hitapi == 'success'){
                                $data['status'] = 'pending';
                                $m = Message::create($data);
                                CampaignMessage::create([
                                    'message_id' => $m->id,
                                    'campaign_id' => $c->id,
                                ]);
                                Auth::user()->update(['sms' => $sum]);
                            }else{
                                return redirect()->back()->withErrors($hitapi);
                            }     
                        }
                    }
                }
            }
        }else{
            if($request->type == 'single'){
                $hitapi = $this->hitApi($data);
                if($hitapi == 'success'){
                    $data['status'] = 'successfully';
                    $data['type'] = 'single';
                    
                    $sumSms = Auth::user()->sms - 1;
                    Auth::user()->update(['sms' => $sumSms ]);
                    Message::create($data);
                }else{
                    return redirect()->back()->withErrors($hitapi);
                }     
            }else{
                $data['campaign'] = $request->campaign;
                $data['file'] = $request->file;
                $data['campaign_status'] = 'completed';
                $readExcel = Excel::toArray(new BulkSmsImport, $request->file);
                $c = $this->save_campaign($data);
                foreach ($readExcel as $_read) {
                    if(count($_read) > 0){
                        $sum = Auth::user()->sms - count($_read);
                        foreach ($_read as $r) {
                            $data['contact_number']= (int)$r['number'];
                            $hitapi = $this->hitApi($data);
                            if($hitapi == 'success'){
                                $data['status'] = 'successfully';
                                $m = Message::create($data);
                                CampaignMessage::create([
                                    'message_id' => $m->id,
                                    'campaign_id' => $c->id,
                                ]);
                                Auth::user()->update(['sms' => $sum]);
                            }else{
                                return redirect()->back()->withErrors($hitapi);
                            }     
                        }
                    }
                }
            }
        }
        
        return redirect()->back()->with('success','Message Sended Successfully!');
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
        return 'success';
    }

    public function campaignFileDownload($id)
    {
        $campaign = Campaign::findOrFail(decrypt($id));
        return  Storage::download($campaign->file_url);
    }
  
}
