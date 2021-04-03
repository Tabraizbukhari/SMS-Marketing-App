<?php

namespace App\Http\Controllers\DashboardControllers\AuthControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class loginController extends Controller
{
    public function login(Request $request)
    {
        $password = $request->password;
        $user = User::where('name', $request->username)->first();
        if($user == null){
            return redirect()->back()->withErrors('incorrect username!'); 
        }
        if(Auth::attempt(['email' => $user->email, 'password' => $password])) {
            return redirect()->route('admin.dashboard');
        }else{
            return redirect()->back()->withErrors('something wents wrong');
        }

    }

    public function loginView(Request $request)
    {
        $user = User::where('login_logo_url', $request->getHttpHost())->first();
        $data['logo_img'] = ($user->logo_img != NULL)? $user->logo_img : NULL;
        return view('dashboard.auth.login', $data);
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
