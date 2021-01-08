<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;

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

        return view('dashboard.index',$data);
    }

    public function loginPage()
    {
        return view('dashboard.auth.login');
    }

}
