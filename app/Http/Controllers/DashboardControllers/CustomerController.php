<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use App\Models\Masking;
use App\Models\UsersData;
use App\Models\UserMasking;
use App\Models\SmsApi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\ResellerCustomer;
use App\Models\Transaction;

class CustomerController extends Controller
{
    public $pagination; 
    public $api_url;
    public function __construct()
    {
        $this->pagination = 10;
        $this->api_url = 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?';
    }
    public function index()
    {

        // $user = User::where('type','user')->whereHas('getUserData', function ($query)
        // {
        //     $query->where('register_as', 'customer');
        // })->paginate($this->pagination);

        $data['user'] = Auth::user()->getResellerCustomerProfit;

        return view('dashboard.customer.index', $data);
    }

    public function create()
    {
        $data['maskings'] = (Auth::user()->type == 'user')? Auth::user()->getResellerMasking : Masking::get();
        return view('dashboard.customer.create', $data);
    }


    public function store(Request $request)
    {
        if(Auth::user()->sms == 0){
            return redirect()->back()->withErrors('You have no more sms');
        }

        $data = [
            'name'              =>  $request->username,
            'email'             =>  $request->email,
            'password'          =>  Hash::make($request->password) ,
            'sms'               =>  $request->sms,
            'price'             =>  $request->cost,
            'type'              =>  'user',
            'email_verified_at' =>  now(),
            'api_token'         =>  Str::random('80'),
        ];
        $user = User::create($data);

        ResellerCustomer::create([
            'user_id'   => Auth::id(),
            'customer_id'   => $user->id,
        ]);
        
        if($request->has('masking')){
            UserMasking::create([
                'user_id' => $user->id, 
                'masking_id' => $request->masking
                ]);
        }

        $users_data = [
            'user_id'       => $user->id,
            'phone_number'  => $request->phone_number,
            'register_as'   => 'customer',
        ];
        UsersData::create($users_data);
        
        if($request->has('api_name') && $request->has('api_password')){
            SmsApi::create([
                'user_id'       => $user->id,
                'api_url'       => $this->api_url,
                'api_username'  => $request->api_name,
                'api_password'  => $request->api_password,
            ]);
        }

         
        $count = Auth::user()->sms - $request->sms;
        Auth::user()->update(['sms'=> $count]);
        Transaction::create([
            'transaction_id' => Auth::user()->getTransactionId(),
            'user_id' => Auth::user()->id,
            'title' => 'transfer sms',
            'description' => 'Transfer sms into new customer '.$request->username,
            'amount' =>  $request->sms,
            'type' => 'debit',
            'data' => json_encode(['user_id' => $user->id]),
        ]);

        Transaction::create([
            'transaction_id' => Auth::user()->getTransactionId(),
            'user_id' => $user->id,
            'title' => 'received sms',
            'description' => 'Received sms by '. Auth::user()->name,
            'amount' =>  $request->sms,
            'type' => 'credit',
        ]);

      
        return redirect()->route('customer.index')->with('success','Customer Created Successfully');
    }
    

    public function destroy($id)
    {
        $user = User::findOrFail(decrypt($id));
        if($user->getAllMessages()->count() > 0){
            return redirect()->back()->withErrors('You can not delete this customer');
        }
        $smscount = Auth::user()->sms + $user->sms;
        Auth::user()->update(['sms' => $smscount]);
        Transaction::create([
            'transaction_id' => Auth::user()->getTransactionId(),
            'user_id' => Auth::user()->id,
            'title' => 'delete customer return',
            'description' => 'Delete account and Return sms to customer '.$user->name,
            'amount' =>  $user->sms,
            'type' => 'credit',
            'data' => json_encode(['user_id' => $user->id]),
        ]);
        $user->delete();
        return redirect()->back()->with('success','Customer deleted Successfully');
    }

    public function edit($id)
    {
        $data['user'] = User::findOrFail(decrypt($id));
        $data['maskings'] = (Auth::user()->type == 'user')? Auth::user()->getResellerMasking : Masking::get();
        return view('dashboard.customer.edit',$data);
    }



    public function update(Request $request,$id)
    {
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|email',
            'sms' => 'required|numeric',
            'cost' => 'required',
            'phone_number' => 'required|numeric',
            'api_name' => 'required',
            'api_password' => 'required',
        ]);

        if($request->sms > Auth::user()->sms){
            return redirect()->back()->withErrors('you have not enough sms');
        }
        $user = User::findOrFail(decrypt($id));
        $userSmsCount = $user->sms;
        $data = [
            'name'              =>  $request->username,
            'email'             =>  $request->email,
            'price'             =>  $request->cost,
        ];
        if($request->has('password') && $request->password != null){
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        if($request->has('masking')){
            UserMasking::where('user_id', decrypt($id))->update(['masking_id' => $request->masking]);
        }

        $users_data = [
            'phone_number'  => $request->phone_number,
            'register_as'   => 'customer',
        ];
        UsersData::where('user_id', decrypt($id))->update($users_data);

        if($request->has('api_name') && $request->has('api_password')){
            SmsApi::where('user_id', decrypt($id))->update([
                'api_username'  => $request->api_name,
                'api_password'  => $request->api_password,
            ]);
        }
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

        return redirect()->route('customer.index')->with('success','Update customer successfully');
    }

}
