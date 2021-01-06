<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Masking;
use Auth;


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
        $data = [
            'user_id'           => Auth::id(),
            'masking_id'        => $request->masking_id,
            'message'           => $request->message,
            'message_length'    => $request->no_of_sms,
        ];
        $data['masking_name'] = Masking::findOrFail($request->masking_id)->title;
        if($request->type == 'single'){
            
            $data['contact_number'] = $request->phone_number;
            if(!$request->has('late_shedule') && $request->late_shedule == null){
                $ch = curl_init($this->message_url($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result=  curl_exec($ch);
                if($result == false){
                    return redirect()->back()-withErrors(curl_error($ch));
                }
            }
        }

        $error = (isset($result))? json_decode($result): null;
        if(isset($error) && $error != null){
            if(!isset($error->Data->status)){
                return redirect()->back()->withErrors($result);
            }
        }
        
        $this->save_message($data);
        return redirect()->back()->with('success','Message Sended Successfully!');
    }

    public function message_url($data)
    {
        $url = Auth::user()->getUserSmsApi->api_url;
        $url .= 'user='.Auth::user()->getUserSmsApi->api_username;
        $url .= '&pwd='.Auth::user()->getUserSmsApi->api_password;
        $url .= '&sender='.$data['masking_name'];
        $url .= '&reciever='.$data['contact_number'];
        $url .= '&msg-data='.$data['message'];
        $url .= '&response=json';
        return $url;
    }

    public function save_message($data)
    {
        $create = Message::create($data);
        return;
    }


}
