<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncommingController extends Controller
{
    public function index(Request $request)
    {

        return response()->json($request->all());
        // dd($request->all());
    }
}
