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
                IncomingMessage::create([
                    'user_id'       => $user->id,
                    'sender'        => $request->sender,
                    'receiver'      => $request->receiver,
                    'body'          => $request->msgdata,
                    'recvtime'      => $request->recvtime,
                    'message_id'    => $request->msgid,
                    'operator'      => $request->operator,
                ]);

                $response['response'] = "Received successfully!";
                $response['success'] = true;
            }
        }
        return response()->json($response);
    }
}
