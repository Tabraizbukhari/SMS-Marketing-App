<?php

namespace App\Http\Controllers\DashboardControllers\AuthControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
        use Illuminate\Support\Facades\Auth;
class loginController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        if(Auth::attempt(['email' => $email, 'password' => $password])) {
            return redirect()->route('admin.dashboard');
        }else{
            return redirect()->back()->withErrors('something wents wrong');
        }

    }

    public function loginView($type)
    {
        $data['type'] = $type;
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
