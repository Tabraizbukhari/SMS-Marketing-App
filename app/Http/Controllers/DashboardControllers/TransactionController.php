<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Auth;

class TransactionController extends Controller
{
    public $pagination; 
    public function __construct()
    {
        $this->pagination = 10;
    }
    public function index()
    {
        $data['transactions'] = Transaction::where('user_id', Auth::id())->orderBy('id','desc')->paginate($this->pagination);
        return view('dashboard.transaction.index', $data);
    }
}
