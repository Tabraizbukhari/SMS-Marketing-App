<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
   
    public function index()
    {
        return view('dashboard.index');
    }

    public function loginPage()
    {
        return view('dashboard.auth.login');
    }

}
