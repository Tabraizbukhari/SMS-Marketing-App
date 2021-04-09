<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Masking;
use App\Models\UsersData;
use App\Models\UserMasking;
use App\Models\SmsApi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Auth;
use App\Models\Transaction;
use Carbon\Carbon;
use DB;

class ResellerController extends Controller
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
        $user = User::where('type','user')->whereHas('getUserData', function ($query)
        {
            $query->where('register_as', 'reseller');
        })->withCount('getResellerCustomer as customer_count')
        ->withCount(['getAllMessages AS myprofit' => function ($query) {
                $query->select(DB::raw('SUM(price) as profit'));
            }])->paginate($this->pagination);;
        $data['user'] = $user;
        return view('dashboard.reseller.index', $data);
    }

    public function resellerCustomer($id)
    {
        $user = User::findOrFail(decrypt($id));
        $data['user'] = $user->getResellerCustomerProfit;
        return view('dashboard.customer.index', $data);
    }

    public function create()
    {
        $data['maskings'] = Masking::get();
        return view('dashboard.reseller.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'      => 'required|string',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required',
            'sms'           => 'required|numeric',
            'cost'          => 'required',
            'api_name'      => 'required',
            'api_password'  => 'required',
            'api_url'       => 'sometimes|required',
            'masking'       => 'sometimes|required|array',
        ]);
        
        if(Auth::user()->sms == 0){
            return redirect()->back()->withErrors('You have enough not sms balance');
        }
        $count = Auth::user()->sms - $request->sms;
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

        if($request->has('masking') && count($request->masking) > 0){
            foreach ($request->masking as $mask) {
                UserMasking::create([
                        'user_id' => $user->id, 
                        'masking_id' => $mask
                ]);
            }
        }

        $users_data = [
            'user_id'       => $user->id,
            'phone_number'  => $request->phone_number,
            'register_as'   => 'reseller',
        ];
        UsersData::create($users_data);
        $admin = User::where('type', 'admin')->firstOrFail();
        if($request->has('api_name') && $request->has('api_password')){
            SmsApi::create([
                'user_id'       => $user->id,
                'api_url'       => $request->api_url??$this->api_url,
                'api_username'  => $request->api_name??$admin->getUserSmsApi->api_username,
                'api_password'  => $request->api_password??$admin->getUserSmsApi->api_password,
                'type'          => ($request->api_url)? 'code' : 'masking',
            ]);
        }
        
        Transaction::create([
            'transaction_id' => Auth::user()->getTransactionId(),
            'user_id' => Auth::user()->id,
            'title' => 'transfer sms',
            'description' => 'Transfer sms into new reseller '.$request->username,
            'amount' =>  $request->sms,
            'type' => 'debit',
            'data' => json_encode(['user_id' => $user->id]),
        ]);

        Transaction::create([
            'transaction_id' => Auth::user()->getTransactionId(),
            'user_id' => $user->id,
            'title' => 'received sms',
            'description' => 'Received sms by Admin',
            'amount' =>  $request->sms,
            'type' => 'credit',
        ]);
           
        Auth::user()->update(['sms' => $count]);
        return redirect()->route('admin.reseller.index')->with('success','Reseller Created Successfully');
    }
    

    public function destroy($id)
    {
        $user = User::findOrFail(decrypt($id));
            if($user->getAllMessages()->count() > 0){
                return redirect()->back()->withErrors('You can not delete this reseller');
            }
        $smscount = Auth::user()->sms + $user->sms;
        Auth::user()->update(['sms' => $smscount]);
        Transaction::create([
            'transaction_id' => Auth::user()->getTransactionId(),
            'user_id' => Auth::user()->id,
            'title' => 'delete reseller return',
            'description' => 'Delete account and Return sms to reseller '.$user->name,
            'amount' =>  $user->sms,
            'type' => 'credit',
            'data' => json_encode(['user_id' => $user->id]),
        ]);
        $user->delete();
        return redirect()->back()->with('success','Reseller deleted Successfully');
    }

    public function edit($id)
    {
        $data['user'] = User::findOrFail(decrypt($id));
        $data['maskings'] = Masking::get();
        return view('dashboard.reseller.edit',$data);
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
            return redirect()->back()->withErrors('Admin have not enough sms');
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
            UserMasking::where('user_id', decrypt($id))->delete();
            foreach ($request->masking as $mask) {
                UserMasking::create([
                    'user_id' =>decrypt($id), 
                    'masking_id' => $mask
                ]);
            }
        }

        $users_data = [
            'phone_number'  => $request->phone_number,
            'register_as'   => 'reseller',
        ];
        UsersData::where('user_id', decrypt($id))->update($users_data);

        $admin = User::where('type', 'admin')->firstOrFail();
        $apiUsername = ($request->api_name != NULL)? $request->api_name : $admin->getUserSmsApi->api_username;
        $apiPassword = ($request->api_password != NULL)? $request->api_password : $admin->getUserSmsApi->api_username;
        SmsApi::where('user_id', decrypt($id))->update([
            'api_username'  =>  $apiUsername,
            'api_password'  =>  $apiPassword,
        ]);
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

        return redirect()->route('admin.reseller.index')->with('success','Update reseller successfully');
    }

    
}
