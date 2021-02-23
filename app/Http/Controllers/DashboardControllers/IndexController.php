<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Message;
use DB;
use Carbon\Carbon;
use App\Models\Notifiable; 

class IndexController extends Controller
{
   
    public function index()
    {
        $data['sms_count'] = Auth::user()->sms;
        $data['reseller_count'] = User::where('type', 'user')->whereHas('getUserData', function ($query)
                                    {
                                        $query->where('register_as', 'reseller');
                                    })->count();

        $data['customer_count'] = Auth::user()->getResellerCustomer->count();
        $data['total_message_sending'] =  (Auth::user()->type == 'admin')? Message::count(): Message::where('user_id', Auth::id())->count();
        $data['total_message_transfer'] = User::where('type','user')->sum('sms');
        $data['profit'] = (Auth::user()->type == 'user')? Message::where('user_id', Auth::id())->sum('price') : Message::sum('price');
        $data['message_send_code'] = Message::where('api_type', 'code')->count();
        $data['message_send_masking'] = Message::where('api_type', 'masking')->count();

        return view('dashboard.index',$data);
    }

    public function loginPage()
    {
        return view('dashboard.auth.login');
    }

    public function updateAdminSms(Request $request)
    {
        Auth::user()->update(['sms' => $request->sms]);
        return redirect()->back()->with('success', 'Sms limit updated successfully');
    }

    public function getapi()
    {   
        if(Auth::user()->getUserSmsAPi->type == 'masking'){
            $data['api_url'] =  'http://161.35.238.65/SMA/api/sendmessage?email='.Auth::user()->email.'&message=testing&phone_number=090049124&orginator=your masking';
        }else{
            $data['api_url'] =  'http://161.35.238.65/SMA/api/sendmessage?email='.Auth::user()->email.'&message=write&phone_number=090049124&orginator=your code';
        }
        return view('dashboard.api.index', $data);
    }

    public function message_url($data)
    {
        $url = $data->api_url;
        $url .= 'user='.$data->api_username;
        $url .= '&pwd='.$data->api_password;
        $url .= '&sender='.NULL;
        $url .= '&reciever='.NULL;
        $url .= '&msg-data='.NULL;
        $url .= '&response=json';
        return $url;
    }

}
