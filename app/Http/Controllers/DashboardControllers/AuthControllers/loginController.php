<?php

namespace App\Http\Controllers\DashboardControllers\AuthControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UsersData;

class loginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
    }

    public function login(Request $request)
    {
        $password = $request->password;
        $username = $request->username;
        if(Auth::attempt(['username' => $username, 'password' => $password])) {
            return redirect()->route('user.dashboard');
        }else{
            return redirect()->back()->withErrors('something wents wrong');
        }

    }

    public function loginView(Request $request)
    {
        $user = UsersData::where('login_url', $request->getHttpHost())->first();
        $data['logo_img'] = (isset($user['logo_img']))? $user->logo_img : NULL;
        return view('dashboard.auth.login', $data);
    }


  

}
