<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersData;
use App\Models\Masking;
use App\Models\SmsApi;
use Carbon\Carbon;
use App\Models\UserMasking;
use App\Models\ResellerCustomer;

use Auth;
use App\Models\Transaction;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public $pagination; 
    public function __construct()
    {
        $this->pagination = 10;
    }

    public function index()
    {
        $data['user'] =  Auth::user()->getResellerCustomer()->paginate($this->pagination);
        return view('dashboard.customer.index', $data);
    }

    public function create($type = NULL){
        $data['type'] = $type;
        if($type == NULL){
            abort(404);
        }elseif(Auth::user()->type != $type){
            abort(404);
        }elseif ($type == 'masking') {
            $data['maskings'] = Auth::user()->getResellerMasking;
        }else{
            $data['code'] = '99095';
        }
        return view('dashboard.customer.create', $data);
    }

    public function store(Request $request, $type){
   
        if(Auth::user()->UserData->has_sms < $request->sms){
            return redirect()->back()->withErrors('your have not enough balance ');
        }

        $request->validate([
            "first_name"    => "required|string",
            "last_name"     => "required|string",
            "email"         => "required|email",
            "password"      => "required|min:5",
            "cost"          => "required",
            "sms"           => "required",
            "code"          => "sometimes|required|numeric",
            "api_url"       => "sometimes|required",
            'masking.*'     => "sometimes|required",
            // 'register_as'   => "required"
        ]);

        $user = User::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'username'          => $request->first_name,
            'email_verified_at' => Carbon::now(),
            'password'          => Hash::make($request->password),
            'api_token'         => Str::random('20'),
            'phone_number'      => $request->phone_number,
            'type'              => $type,
            'register_as'       => 'customer',
            'reference_id'      => Auth::id(),
        ]);

        ResellerCustomer::create([
            'user_id' => Auth::id(),
            'customer_id' => $user->id,
        ]);
        UsersData::create([
            'user_id'           =>  $user->id,
            'has_sms'           =>  $request->sms,
            'price_per_sms'     =>  $request->cost,
            'Invoice_charges'   =>  $request->invoice_cost,
        ]);

        if($request->has('masking') && count($request->masking) > 0){
            foreach ($request->masking as $mask) {
                UserMasking::create([
                    'user_id'     => $user->id, 
                    'masking_id'  => $mask,
                ]);
            }
        }

     
        $smsApiData['user_id'] = $user->id;
        $smsApiData['api_url']      = $request->api_url??NULL;
        $smsApiData['api_username'] = $request->api_name??NULL;
        $smsApiData['api_password'] = $request->api_password??NULL;
    
        SmsApi::create($smsApiData);

        Transaction::create([
            'transaction_id' => Auth::id(),
            'user_id' => Auth::id(),
            'title' => 'transfer sms',
            'description' => 'Transfer sms into new customer '.$user->full_name,
            'amount' =>  $request->sms,
            'type' => 'debit',
            'data' => json_encode(['user_id' => $user->id]),
        ]);

        Transaction::create([
            'transaction_id' => Auth::id(),
            'user_id' => $user->id,
            'title' => 'received sms',
            'description' => 'Received sms by Admin',
            'amount' =>  $request->sms,
            'type' => 'credit',
        ]);
     
        $SmsUpdate = Auth::user()->UserData->has_sms - $request->sms;
        $auth = Auth::user()->UserData()->update([
            'has_sms' => $SmsUpdate,
        ]);
        return redirect()->route('user.customer.index')->with('success', 'Customer Created Successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail(decrypt($id));
        if($user->type == 'masking'){
            $data['maskings'] = Auth::user()->getResellerMasking;
        }else{
            $data['code'] = 99095;
        }
        $userMasking = [];
        foreach ($user->getResellerMasking as $masking) {
            array_push($userMasking, $masking->id);
        }
        $data['userMasking']  = $userMasking;
        $data['user'] = $user;
        return view('dashboard.customer.edit', $data);

    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $request->validate([
            "first_name"    => "required|string",
            "last_name"     => "required|string",
            "email"         => "required|email",
            "cost"          => "required",
            "sms"           => "required",
            "code"          => "sometimes|required|numeric",
            "api_url"       => "sometimes|required",
            'masking.*'     => "sometimes|required"
        ]);

        $user = User::findOrFail($id);
        $userPervioussms = $user->UserData->has_sms;
        $user->update([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'username'          => $request->first_name,
            'password'          => Hash::make($request->password),
            'api_token'         => Str::random('20'),
            'phone_number'      => $request->phone_number,
            'reference_id'      => Auth::id(),
        ]);

        $user->UserData()->update([
            'has_sms'           =>  $request->sms,
            'price_per_sms'     =>  $request->cost,
            'Invoice_charges'   =>  $request->invoice_cost,
        ]);

        if($request->has('masking') && count($request->masking) > 0){
            foreach ($request->masking as $mask) {
                UserMasking::where('user_id', $user->id)->updateOrCreate([
                    'masking_id'  => $mask,
                ]);
            }
        }

        $smsApiData['api_url']      = $request->api_url??NULL;
        $smsApiData['api_username'] = $request->api_name??NULL;
        $smsApiData['api_password'] = $request->api_password??NULL;
    
        SmsApi::where('user_id', $user->id)->update($smsApiData);

        if($userPervioussms != $request->sms){
            if($userPervioussms > $request->sms){
                $count = $userPervioussms - $request->sms;
                $adminSmsUpdate = Auth::user()->has_sms + $count;
                Transaction::create([
                    'transaction_id' => Auth::id(),
                    'user_id' => Auth::id(),
                    'title' => 'Return Sms',
                    'description' => 'Return sms to reseller '.$user->full_name,
                    'amount' =>  $count,
                    'type' => 'credit',
                    'data' => json_encode(['user_id' => $user->id]),
                ]);

                Transaction::create([
                    'transaction_id' => Auth::id(),
                    'user_id' => $user->id,
                    'title' => 'Deduct Sms',
                    'description' => 'Deduct sms by Admin',
                    'amount' =>  $request->sms,
                    'type' => 'debit',
                ]);


            }else{
                $count = $request->sms - $userPervioussms;
                $adminSmsUpdate = Auth::user()->has_sms - $count;

                Transaction::create([
                    'transaction_id' => Auth::user()->getTransactionId(),
                    'user_id' => Auth::user()->id,
                    'title' => 'transfer sms',
                    'description' => 'Transfer sms into reseller '.$user->full_name,
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
            $auth = Auth::user()->update([
                'has_sms' => $adminSmsUpdate,
            ]);
        }
        return redirect()->route('user.customer.index')->with('success', 'Reseller Updated Successfully');
    }


    public function destroy (Request $request, $id)
    {
        $user = User::findOrFail(decrypt($id));

        if($user->getAllMessages()->count() > 0){
            return redirect()->back()->withErrors('You can not delete this reseller');
        }

        $smscount = Auth::user()->UserData->has_sms + $user->UserData->has_sms;
        Auth::user()->UserData()->update(['has_sms' => $smscount]);
        Transaction::create([
            'transaction_id' => Auth::id(),
            'user_id' => Auth::id(),
            'title' => 'delete reseller return',
            'description' => 'Delete account '.$user->full_name.' and Return sms to reseller '.$user->name,
            'amount' =>  $user->UserData->has_sms,
            'type' => 'credit',
            'data' => json_encode(['user_id' => $user->id]),
        ]);
        $user->delete();
        return redirect()->back()->with('success', 'User Deleted Successfully');
    }


    public function addCustomerAmount(Request $request, $id)
    {
        $request->validate(['amount' => 'required']);
        $user = User::findOrFail(decrypt($id));
        $amount = $user->UserData->has_sms + $request->amount;
      
        Transaction::create([
            'transaction_id' => Auth::id(),
            'user_id'     => Auth::id(),
            'title'       => 'transfer sms',
            'description' => 'Transfer sms into reseller '.$user->username,
            'amount'      =>  $request->amount,
            'type'        => 'debit',
            'data'        => json_encode(['user_id' => $user->id]),
        ]);


        Transaction::create([
            'transaction_id' => $user->id,
            'user_id'     => $user->id,
            'title'       => 'received sms',
            'description' => 'Received sms by'.$user->username,
            'amount'      =>  $request->amount,
            'type'        => 'credit',
            'data'        => json_encode(['user_id' => $user->id]),
        ]);

        $user->UserData()->update([
            'has_sms'   => $amount,
        ]);

        $authSms = Auth::user()->UserData->has_sms - $request->amount;
        Auth::user()->UserData()->update([
            'has_sms'=> $authSms,
        ]);

        return redirect()->back()->with('success', 'Add Amount Successfully');
    }
}
