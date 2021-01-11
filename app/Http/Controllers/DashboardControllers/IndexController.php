<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Message;
use DB;
use Carbon\Carbon;

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

}
