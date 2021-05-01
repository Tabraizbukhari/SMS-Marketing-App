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
use App\Models\Transaction;

class IndexController extends Controller
{
    public function index()
    {
        $data['has_sms'] = Auth::user()->UserData->has_sms;
        // $data['total_sentmessages'] = Message::where('user_id', Auth::id())->where('status', 'successfully')->count();
        $totalMessage = Message::where('user_id', Auth::id())->where('status', 'successfully')->count();
        $messageLength = Message::where('user_id', Auth::id())->where('status', 'successfully')->sum('message_length');
        $data['total_sentmessages'] = $totalMessage;
        $data['total_sentmessagesamount'] = $messageLength;

        $data['failed_messages'] = Message::where('user_id', Auth::id())->where('status', 'not_sent')->count();

        $data['total_transcation'] = Transaction::where('user_id', Auth::id())->where('type', 'credit')->sum('amount');

        // $data['reseller_count'] = User::where('type', 'user')->whereHas('getUserData', function ($query)
        //                             {
        //                                 $query->where('register_as', 'reseller');
        //                             })->count();

        // $data['customer_count'] = Auth::user()->getResellerCustomer->count();
        // $data['total_message_sending'] =  (Auth::user()->type == 'admin')? Message::count(): Message::where('user_id', Auth::id())->count();
        // $data['total_message_transfer'] = User::where('type','user')->sum('sms');
        // $data['profit'] = (Auth::user()->type == 'user')? Message::where('user_id', Auth::id())->sum('price') : Message::sum('price');
        // $data['message_send_code'] = Message::where('api_type', 'code')->count();
        // $data['message_send_masking'] = Message::where('api_type', 'masking')->count();
        // $data['message_send_successfully'] = Message::where('status', 'successfully')->where('user_id', Auth::id())->count();
        // $data['message_not_send'] = Message::where('status', 'not_sent')->where('user_id', Auth::id())->count();

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
        if(Auth::user()->type == 'masking'){
            $data['api_url'] =  'http://sms1.synctechsol.com/api/sendmessage?username='.Auth::user()->username.'&password='.Auth::user()->api_token.'&message=testing&phone_number=090049124&orginator=masking';
        }else{
            $data['api_url'] =  'http://sms1.synctechsol.com/api/sendmessage?username='.Auth::user()->username.'&password='.Auth::user()->api_token.'&message=write&phone_number=090049124&orginator=99095';
        }
        return view('dashboard.api.index', $data);
    }

    public function updateLogo(Request $request)
    {
        $request->validate(['logo' => 'required|image']);
        if($request->hasFile('logo')){
            $file = $request->file('logo');
            $file_path = $file->store(Auth::user()->getLogoUrlPath());
            Auth::user()->UserData()->update([
                'logo_img' => $file_path,
            ]);

            return redirect()->back()->with('success', 'Logo updated successfully');
        }
            return  redirect()->back()->withError('something wents wrong, Try Again');;
    }


    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // return redirect()->route('login','admin');
        return redirect()->back();
    }
}
