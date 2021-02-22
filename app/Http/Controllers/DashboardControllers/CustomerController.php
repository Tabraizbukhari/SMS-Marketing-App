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
use App\Notifications\CustomerRegisterNotification;
use DB;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public $pagination; 
    public $api_url;
    
    public function __construct()
    {
        $this->pagination = 10;
        $this->api_url = 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?';
        $this->api_username = 'synctech';
        $this->api_password = 'AAUKHlNrDzuEHqqE9yVO%2bUKQlEwe94F9npa7WmUWJ1q1rwe7shghLUEs1jctgTbxPQ%3d%3d';

    }

    public function index($id = null)
    {
        $id = decrypt($id);
        if($id['user_id']){
            $data['user'] = User::where('id', $id['user_id'])->get();
            DB::table('notifications')->where('id', $id['notification_id'])->update(['read_at' => Carbon::now() ]);
        }else{
            $data['user'] = ($id)??Auth::user()->getResellerCustomerProfit;
        }
        
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

        $request->validate([
            'name'          => 'required|unique:users',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required',
            'cost'          => 'required',
            'sms'           => 'required|Numeric',
            'api_url'       => 'sometimes|required',
            'masking'       => 'sometimes|required',
        ]);

        if(Auth::user()->getUserSmsApi->type != 'code'){
            $request->validate([
                'api_name'      => 'required',
                'api_password'  => 'required',
            ]);
        }

        $data = [
            'name'              =>  $request->name,
            'email'             =>  $request->email,
            'password'          =>  Hash::make($request->password) ,
            'sms'               =>  $request->sms,
            'price'             =>  $request->cost,
            'type'              =>  'user',
            'email_verified_at' =>  now(),
            'api_token'         =>  Str::random('80'),
        ];

        $user = User::create($data);
        $user->notify(new CustomerRegisterNotification($data));
        ResellerCustomer::create([
            'user_id'   => Auth::id(),
            'customer_id'   => $user->id,
        ]);
        
        if($request->has('masking') && $request->masking != null){
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
        
        if(Auth::user()->getUserSmsApi->type != 'code'){
            SmsApi::create([
                'user_id'       => $user->id,
                'api_url'       => $request->api_url??$this->api_url,
                'api_username'  => $request->api_name??$this->api_username,
                'api_password'  => $request->api_password??$this->api_password,
                'type'          => ($request->api_url)? 'code' : 'masking',
            ]);
        }
         
        
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
        $count = Auth::user()->sms - $request->sms;
        Auth::user()->update(['sms'=> $count]);
    
        

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
        $data['maskings'] = Masking::get();
        return view('dashboard.customer.edit',$data);
    }



    public function update(Request $request,$id)
    {
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|string|email',
            'sms' => 'required|numeric',
            'cost' => 'required',
            'phone_number' => 'required',
            'api_name' => 'required',
            'api_password' => 'required',
            'api_url' => 'required',
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
                'api_url'       => $this->getApiUrl($request->api_url),
                'api_username'  => $request->api_name,
                'api_password'  => $request->api_password,
                'type'       => $this->getApiUrl($request->api_url, "status"),
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


    public function apiCreateORUpdate($id, Request $request)
    {
        $user = User::findOrFail(decrypt($id));
        $request->validate([
            'api_name'      => 'required',
            'api_password'  => 'required',
            'api_url'       => 'required',
        ]);

        
        if($request->has('api_name') && $request->has('api_password')){
            SmsApi::updateOrCreate([
                'user_id'       => $user->id,
                'api_url'       => $request->api_url,
                'api_username'  => $request->api_name,
                'api_password'  => $request->api_password,
                'type'          => ($request->api_url)? 'code' : 'masking',
            ]);
        }

        return redirect()->back()->with('success', 'Api created successfully');
    }
}
