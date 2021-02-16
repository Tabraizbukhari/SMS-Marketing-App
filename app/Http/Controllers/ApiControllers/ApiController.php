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
                'email'         => 'required',
                'message'       => 'required',
                'phone_number'  => 'required',
                'orginator'     => 'required',
            ];
            
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                 $response['response'] = $validator->messages()->first();
            }else if(!User::where('email', $request->email)->exists()){
                $response['response'] = "User doesn't exists";
            }else{
                
                $user = (Auth::check())? Auth::user() : User::where('email', $request->email)->firstOrFail();
                $data = [
                    'user_id'        => $user->id,
                    'message'        => $request->message,
                    'message_length' => strlen($request->message),
                    'contact_number' => ($request->has('phone_number'))? $request->phone_number : NULL,
                    'send_date'      => ($request->has('sheduledatetime') && !empty($request->sheduledatetime))? $request->sheduledatetime : Carbon::now(),
                    'price'          => $user->price,
                ];

                if($user->getUserSmsAPi->type == 'masking'){
                    if(Masking::where('title', $request->orginator)->exists()){
                        $data['masking_name'] = Masking::where('title', $request->orginator)->first()->title;
                    }else{
                        $response['response'] = 'Masking not found';
                        return response()->json($response);
                    }
                }elseif ($request->orginator != 99059) {
                    $response['response'] = 'incorrect code of orginator';
                    return response()->json($response);
                }  
                $hitapi = $this->hitApi($data, $user);
                if($hitapi == 'success'){
                    $data['status'] = 'successfully';
                    $data['type'] = 'single';
                    $sumSms = $user->sms - 1;
                    $user->sms = $sumSms;
                    $user->save();
                    Message::create($data);
                    $response['response'] = "Message Send Successfully";
                    $response['success'] = true;
                }else{
                    $response['response'] = $hitapi;
                }     
            }
            return response()->json($response);
    }

    public function message_url($data, $user)
    {
        $url = $user->getUserSmsApi->api_url;
        if($user->getUserSmsAPi->type == 'masking'){
            $url .= 'user='.$user->getUserSmsApi->api_username;
            $url .= '&pwd='.$user->getUserSmsApi->api_password;
            $url .= '&sender='.$data['masking_name'];
            $url .= '&reciever='.$data['contact_number'];
            $url .= '&msg-data='.$data['message'];
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
        $error = (isset($result))? json_decode($result): null;
        if(isset($error) && $error != null){
            if(!isset($error->Data->status)){
                return $error->Data;
            }
        }
        return 'success';
    }

}
