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
        $data['messages'] = Message::paginate($this->pagination);
        return view('dashboard.messages.index', $data);
    }

    public function create()
    {
        $data['maskings']  =(Auth::user()->type == 'admin')? Masking::get() : Auth::user()->getResellerMasking ;
        return view('dashboard.messages.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'no_of_sms' => 'required',
            'phone_number' => 'sometimes|required',
            'file'          =>  'sometimes|required|file',
            'type' => 'required|string'
        ]);

        $data = [
            'user_id'           => Auth::id(),
            'masking_id'        => $request->masking_id,
            'message'           => $request->message,
            'message_length'    => $request->no_of_sms,
            'contact_number'    => ($request->has('phone_number'))? $request->phone_number : NULL,
            'send_date'         => ($request->has('sheduledatetime') && !empty($request->sheduledatetime))? $request->sheduledatetime : Carbon::now(),
        ];
        $data['masking_name'] = Masking::findOrFail($request->masking_id)->title;


        if($request->has('late_shedule') && $request->has('sheduledatetime') && $request->sheduledatetime != null){
            if($request->type == 'single'){
                dd($this->hitApi($data));
            }
            
        }else{
            if($request->type == 'single'){
                $hitapi = $this->hitApi($data);
                if($hitapi == 'success'){
                    $data['status'] = 'successfully';
                    Message::create($data);
                }else{
                    return redirect()->back()->withErrors($hitapi);
                }     
            }else{
                $data['campaign'] = $request->campaign;
                $data['file'] = $request->file;
                $data['campaign_status'] = 'completed';
                $readExcel = Excel::toArray(new BulkSmsImport, $request->file);
                foreach ($readExcel as $_read) {
                    if(count($_read) > 0){
                        foreach ($_read as $r) {
                            $data['contact_number']= (int)$r['number'];
                            $hitapi = $this->hitApi($data);
                            if($hitapi == 'success'){
                                $data['status'] = 'successfully';
                                $m = Message::create($data);
                                $c = $this->save_campaign($data);
                                CampaignMessage::create([
                                    'message_id' => $m->id,
                                    'campaign_id' => $c->id,
                                ]);
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
        // Auth::user()->getUserSmsApi->api_username
        // Auth::user()->getUserSmsApi->api_password
        $url = Auth::user()->getUserSmsApi->api_url;
        $url .= 'user='.'tabraiz';
        $url .= '&pwd='.'AGJcqrebE%2bDl%2bDv%2bD6uYVQcwau8sinQqJDrpulLwhp3BsqyNWxC6d2Gdywm0CpnVOQ%3d%3d';
        $url .= '&sender='.$data['masking_name'];
        $url .= '&reciever='.$data['contact_number'];
        $url .= '&msg-data='.$data['message'];
        $url .= '&response=json';
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
        dd($result,urlencode($url), $url);
        $error = (isset($result))? json_decode($result): null;
        if(isset($error) && $error != null){
            if(!isset($error->Data->status)){
                return $error->Data;
            }
        }
        return 'success';
    }

  
}
