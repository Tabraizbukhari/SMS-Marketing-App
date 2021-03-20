<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IncomingApi;
use App\Models\IncomingMessage;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class IncommingController extends Controller
{
    
    public function index(Request $request)
    {
        $response = ['success'=>false, 'response' => ''];
        $rules = [
            'sender'    => 'required', 
            'receiver'  => 'required', 
            'msgdata'   => 'required', 
            'recvtime'  => 'required', 
            'msgid'     => 'required', 
            'operator'  => 'required', 
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
         }else{
            $getPrefix = explode(" ",$request->msgdata);
            $user = User::whereHas('IncomingApi', function($q) use($getPrefix){ $q->where('prefix', $getPrefix[0]); })->first();            
            if(!$user){
                $response['response'] = "something wents wrong! try again";
            }else{

                $hitApi = $this->hitApi($request, $user);
                if($hitAp == false){
                    $response['response'] = "Something went's wrong";
                    $response['success'] = false;
                }else{
                    IncomingMessage::create([
                        'user_id'       => $user->id,
                        'sender'        => $request->sender,
                        'receiver'      => $request->receiver,
                        'body'          => $request->msgdata,
                        'recvtime'      => date("Y/m/d H:i:s", strtotime($request->recvtime)),
                        'message_id'    => $request->msgid,
                        'operator'      => $request->operator,
                    ]);

                    $response['response'] = "Received successfully!";
                    $response['success'] = true;
                }
            }
        }
        return response()->json($response);
    }

    public function hitApi($request, $user)
    {
        $url = $this->message_url($request, $user);
        $url = str_replace(" ", '%20', $url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);        
        $result=  curl_exec($ch);
        $data = json_decode($result, true);
        if($data['status'] == true){
            return true;
        }else{
            return false;   
        }
    }


    public function message_url($request, $user)
    {
        $url  =  $user->IncomingApi->customer_api;
        $url .= 'number='.$request->sender;
        $url .= '&receiver='.$request->receiver;
        $url .= '&msg='.urlencode($request->msgdata);
        $url .= '&recvtime='.date("Y/m/d H:i:s", strtotime($request->recvtime));
        $url .= '&msgid='.$request->msgid;
        // $url .= 'operator'.$request->operator;
        return $url;
    }
}
