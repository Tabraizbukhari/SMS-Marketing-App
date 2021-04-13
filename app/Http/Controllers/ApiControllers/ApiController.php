<?php

namespace App\Http\Controllers\ApiControllers;

use App\Models\User;
use App\Models\Masking;
use App\Models\Message;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\MessageMasking;
use App\Imports\BulkSmsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public $pagination; 
    public $api_url;
    public function __construct()
    {
        $this->api_url = 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?';
    }


    public function store(Request $request)
    {
            $response = ['success'=>false, 'response' => ''];
            $rules = [
                'username'         => 'required',
                'message'       => 'required',
                'phone_number'  => 'required|min:11|max:12',
                'orginator'     => 'required',
                'password'      => 'required',
            ];
            
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                 $response['response'] = $validator->messages()->first();
            }else if(!User::where('username', $request->username)->where('api_token', $request->password)->exists()){
                $response['response'] = "User credentials doesn't match.";
            }else if($this->stringCount($request->message) == false){
                $response['response'] = 'Maximum message length limit is 5';
            }else{
                
                $user = (Auth::check())? Auth::user() : User::where('email', $request->email)->firstOrFail();
               
                if($user->sms == 0){
                    $response['response'] = 'User have zero sms ';
                    return response()->json($response);
                }
                $data = [
                    'user_id'        => $user->id,
                    'message'        => $request->message,
                    'message_length' => $this->stringCount($request->message),
                    'contact_number' => ($request->has('phone_number'))? $request->phone_number : NULL,
                    'send_date'      => ($request->has('sheduledatetime') && !empty($request->sheduledatetime))? $request->sheduledatetime : Carbon::now(),
                    'price'          => $user->price * $this->stringCount($request->message),
                    'api_type'       => 'code',
                
                ];

                if($user->getUserSmsAPi->type == 'masking'){
                    if(Masking::where('title', $request->orginator)->exists()){
                        $data['masking_name'] = Masking::where('title', $request->orginator)->first()->title;
                        $data['api_type'] = 'masking';
                    }else{
                        $response['response'] = 'Masking not found';
                        return response()->json($response);
                    }
                }elseif ($request->orginator != 99095) {
                    $response['response'] = 'incorrect code of orginator';
                    return response()->json($response);
                }
                $hitapi = $this->hitApi($data, $user);
                if($hitapi == 'success'){
                    $data['status'] = 'successfully';
                    $data['type'] = 'single';
                    $sumSms = $user->sms - $this->stringCount($request->message);
                    $user->sms = $sumSms;
                    $user->save();
                    Message::create($data);
                    $response['response'] = "Message Send Successfully";
                    $response['success'] = true;
                }else{
                    $response['response'] = 'Message not send Successfully! please try again!';
                }     
            }
        return response()->json($response);
    }

    public function message_url($data, $user)
    {
        $url = $user->getUserSmsApi->api_url;
        $admin = User::where('type', 'admin')->first();
        $apiUser = ($user->getUserSmsApi->api_username != NULL)? $user->getUserSmsApi->api_username :  $admin->getUserSmsApi->api_username;
        $apiPass = ($user->getUserSmsApi->api_password != NULL)? $user->getUserSmsApi->api_password :  $admin->getUserSmsApi->api_password;

        if($user->getUserSmsAPi->type == 'masking'){
            $url .= 'user='.$apiUser;
            $url .= '&pwd='.$apiPass;
            $url .= '&sender='.urlencode($data['masking_name']);
            $url .= '&reciever='.$data['contact_number'];
            $url .= '&msg-data='.urlencode($data['message']);
            $url .= '&response=json';    
        }else{
            $url .= 'action=sendmessage';
            $url .= '&username='.$user->getUserSmsApi->api_username;
            $url .= '&password='.$user->getUserSmsApi->api_password;
            $url .= '&recipient='.$data['contact_number'];
            $url .= '&originator=99095';
            $url .= '&messagedata='.urlencode($data['message']).'';
            $url .= '&responseformat=html';
        }
        return $url;
    }

    public function hitApi($data, $user)
    {
        $url = $this->message_url($data, $user);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result=  curl_exec($ch);
        if($user->getUserSmsAPi->type == 'masking'){
            $error = (isset($result))? json_decode($result): null;
            if(isset($error) && $error != null){
                if(!isset($error->Data->status)){
                    return $error->Data;
                }
            }
        }
        if($result == true){
            return 'success';
        }else{
            return $result;   
        }
    }


    public function stringCount($message)
    {
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
