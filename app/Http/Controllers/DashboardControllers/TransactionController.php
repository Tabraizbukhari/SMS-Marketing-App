<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Auth;
use App\Models\User;

class TransactionController extends Controller
{
    public $pagination; 
    public function __construct()
    {
        $this->pagination = 10;
    }
    public function index()
    {
        $data['transactions'] = Auth::user()->transactions()->orderBy('id','desc')->paginate($this->pagination);
        return view('dashboard.transaction.index', $data);
    }

    public function addAmount(Request $request, $id)
    {
        $user = User::findOrFail(decrypt($id));
        $sms = $request->sms;
        $type= $request->type;
        $userSmsCount = $user->sms;
            switch ($type) {
                case 'reseller':
                        $this->AddtoReseller($request, $userSmsCount, $user);
                    break;
                
                default:
                        $this->AddtoCustomer($request, $userSmsCount, $user);
                    break;
            }
        return redirect()->back()->with('success', 'Amount Added Successfully');
    }


    public function AddtoReseller($request, $userSmsCount, $user)
    {
        if($userSmsCount != $request->sms){
            if($userSmsCount > $request->sms){
                $count = $userSmsCount - $request->sms;
                $admincount = Auth::user()->sms + $count;
                Transaction::create([
                    'transaction_id' => Auth::user()->getTransactionId(),
                    'user_id' => Auth::user()->id,
                    'title' => 'Return Sms',
                    'description' => 'Return sms to reseller '.$request->username,
                    'amount' =>  $count,
                    'type' => 'credit',
                    'data' => json_encode(['user_id' => $user->id]),
                ]);

                Transaction::create([
                    'transaction_id' => Auth::user()->getTransactionId(),
                    'user_id' => $user->id,
                    'title' => 'Deduct Sms',
                    'description' => 'Deduct sms by Admin',
                    'amount' =>  $request->sms,
                    'type' => 'debit',
                ]);


            }else{
                $count = $request->sms - $userSmsCount;
                $admincount = Auth::user()->sms - $count;

                Transaction::create([
                    'transaction_id' => Auth::user()->getTransactionId(),
                    'user_id' => Auth::user()->id,
                    'title' => 'transfer sms',
                    'description' => 'Transfer sms into reseller '.$request->username,
                    'amount' =>  $count,
                    'type' => 'debit',
                    'data' => json_encode(['user_id' => $user->id]),
                ]);
    
                Transaction::create([
                    'transaction_id' => Auth::user()->getTransactionId(),
                    'user_id' => $user->id,
                    'title' => 'received sms',
                    'description' => 'Received sms by Admin',
                    'amount' =>  $count,
                    'type' => 'credit',
                ]);
            }
            Auth::user()->update(['sms' => $admincount]);
            $user->update(['sms' =>  $request->sms]);
        
        }
        return true;
    }

    public function AddtoCustomer($request, $userSmsCount, $user)
    {

        if($userSmsCount != $request->sms){
            if($userSmsCount > $request->sms){
                $count = $userSmsCount - $request->sms;
                $admincount = Auth::user()->sms + $count;
                Transaction::create([
                    'transaction_id' => Auth::user()->getTransactionId(),
                    'user_id' => Auth::user()->id,
                    'title' => 'Return Sms',
                    'description' => 'Return sms to customer '.$request->username,
                    'amount' =>  $count,
                    'type' => 'credit',
                    'data' => json_encode(['user_id' => $user->id]),
                ]);

                Transaction::create([
                    'transaction_id' => Auth::user()->getTransactionId(),
                    'user_id' => $user->id,
                    'title' => 'Deduct Sms',
                    'description' => 'Deduction sms',
                    'amount' =>  $request->sms,
                    'type' => 'debit',
                ]);


            }else{
                $count = $request->sms - $userSmsCount;
                $admincount = Auth::user()->sms - $count;

                Transaction::create([
                    'transaction_id' => Auth::user()->getTransactionId(),
                    'user_id' => Auth::user()->id,
                    'title' => 'transfer sms',
                    'description' => 'Transfer sms into customer '.$request->username,
                    'amount' =>  $count,
                    'type' => 'debit',
                    'data' => json_encode(['user_id' => $user->id]),
                ]);
    
                Transaction::create([
                    'transaction_id' => Auth::user()->getTransactionId(),
                    'user_id' => $user->id,
                    'title' => 'received sms',
                    'description' => 'Received sms by '.Auth::user()->name,
                    'amount' =>  $count,
                    'type' => 'credit',
                ]);
            }
            Auth::user()->update(['sms' => $admincount]);
            $user->update(['sms' =>  $request->sms]);
        
        }
        return true;
    }
}
