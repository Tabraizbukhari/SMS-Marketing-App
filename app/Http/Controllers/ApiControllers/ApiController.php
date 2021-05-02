<?php

namespace App\Http\Controllers\ApiControllers;

use App\Models\User;
use App\Models\Admin;

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
                'username'      => 'required',
                'message'       => 'required',
                'phone_number'  => 'required|min:10|max:12',
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

                $num = (substr($request->phone_number, 0, 2) == '03')? true : ((substr($request->phone_number, 0, 3) == '923')? true : ((substr($request->phone_number, 0, 1) == "3")? true:false) );
                if($num == false){
                    $response['response'] = 'Plesae Start your number with 92, 03, 3';
                    return response()->json($response);
                }
                    $user = User::Where('username', $request->username)->firstOrFail();

                if($user->UserData->has_sms > $this->stringCount($request->message) && $user->UserData->has_sms == 0){
                    $response['response'] = 'You do not have balance';
                    return response()->json($response);
                }

                $noOfSms =  $this->stringCount($request->message);
                $data = [
                    'user_id'        => $user->id,
                    'message'        => $request->message,
                    'message_length' => $this->stringCount($request->message),
                    'contact_number' => $request->phone_number,
                    'send_date'      => $request->sheduledatetime??Carbon::now(),
                    'price'          => $user->UserData->price_per_sms *  $noOfSms,
                    'api_type'       => $user->type,
                ];

                if($user->type == 'masking'){
                    if(Masking::where('title', $request->orginator)->exists()){
                        $masking = Masking::where('title', $request->orginator)->first();
                        $data['orginator'] = $masking->title;
                        $data['masking_id'] = $masking->id;

                    }else{
                        $response['response'] = 'Incorrect orginator';
                        return response()->json($response);
                    }
                }else{
                    $data['orginator'] = '99095';
                }
                
                $data['type'] = 'single';
                $htiApi = $this->hitApi($data, $user);
                    if($user->type == 'masking'){
                        if(isset($htiApi['Data']['msgid']) && !empty($htiApi['Data']['msgid'])){
                            if(substr($request->phone_number, 0, 3) == '033'){
                                $noOfSms += $noOfSms / 2 + $noOfSms;
                             }
                            $data['price']        = $user->UserData->price_per_sms *  $noOfSms;
                            $data['message_id']   = $htiApi['Data']['msgid'];
                            $data['status']       = 'successfully';
                            $sendMessage          = $this->saveMessage($data, $user);
                            $response['success']  = true;
                            $response['response'] = 'Message send successfully';
                        }else{
                            $response['response'] = $htiApi['Data'];
                        }
                    }else{
                        if(isset($htiApi['data']) && isset($htiApi['data']['acceptreport']['messageid']) && $htiApi['action'] == "sendmessage"){
                            $data['message_id']  = $htiApi['data']['acceptreport']['messageid'];
                            $data['status']      = 'successfully';
                            $sendMessage         = $this->saveMessage($data, $user);
                            $response['success'] = true;
                            $response['response']       = 'Message send successfully';
                        }else if(isset($htiApi['action']) && $htiApi['action'] == "error"){
                            $response['response'] = $htiApi['data']['errormessage'];
                        }else{
                            $response['response'] = 'Something wents wrong! plesae contact your admistrator';
                        }
                    }
     
            }
        return response()->json($response);
    }

    public function message_url($data, $user){   
        $admin    = Admin::first();
        $url      = $user->getUserSmsApi['api_url']??$admin->adminApi->api_url;
        $username = $user->getUserSmsApi['api_username']??$admin->adminApi->api_username;
        $password = $user->getUserSmsApi['api_password']??$admin->adminApi->api_password;
        if($user->type == 'masking'){
            $url .= 'user='.$username;
            $url .= '&pwd='.$password;
            $url .= '&sender='.urlencode($data['orginator']);
            $url .= '&reciever='.$data['contact_number'];
            $url .= '&msg-data='.urlencode($data['message']);
            $url .= '&response=json';
        }else{
            $url .= 'action=sendmessage';
            $url .= '&username='.$user->getUserSmsApi->api_username;
            $url .= '&password='.$user->getUserSmsApi->api_password;
            $url .= '&recipient='.$data['contact_number'];
            $url .= '&originator='.$data['orginator'];
            $url .= '&messagedata='.urlencode($data['message']);
            $url .= '&sendondate='.urlencode(date('Y-m-d h:m:s', strtotime($data['send_date'])));
            $url .= '&responseformat=xml';
        }
        return $url;
    }

    public function hitApi($data, $user)
    {
        $url = $this->message_url($data, $user);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response =  curl_exec($ch);

        if($user->type == 'masking'){
            return json_decode($response, true);
        }
        $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        return $array = json_decode($json,TRUE);
        
    }

    public function AuthSmsCount($messageLength, $user)
    {
        $total_sms = $user->UserData->has_sms - $messageLength;
        return $user->UserData()->update(['has_sms' => $total_sms]);
    }

    public function saveMessage($data, $user){
        $this->AuthSmsCount($data['message_length'], $user);
        $message = Message::create([
            'user_id'           => $data['user_id'],
            'message_id'        => $data['message_id'],
            'message'           => $data['message'],
            'message_length'    => $data['message_length'],
            'contact_number'    => $data['contact_number'],
            'send_date'         => $data['send_date'],
            'type'              => 'single',
            'price'             => $data['price'],
            'api_type'          => $data['api_type'],
            'status'            => $data['status'],
            'reference'         => $data['orginator']
        ]);

        if($data['api_type'] == 'masking'){
            MessageMasking::create([
                'message_id' => $message->id,
                'masking_id' => $data['masking_id'],
            ]);
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
