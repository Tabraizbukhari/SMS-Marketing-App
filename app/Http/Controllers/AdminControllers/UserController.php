<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersData;
use App\Models\Masking;
use App\Models\SmsApi;
use Carbon\Carbon;
use App\Models\UserMasking;
use App\Models\Transaction;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public $pagination;
    public function __construct()
    {
        $this->pagination = 10;
    }

    public function details($id)
    {
        $customer = User::findOrFail(decrypt($id));
        $orginator = '';
        $data['customer'] = $customer;
        if ($customer->type == 'masking') {
            $data['api_url'] =  'http://sms1.synctechsol.com/api/sendmessage?';
            $orginator = 'masking';
        } else {
            $data['api_url'] =  'http://sms1.synctechsol.com/api/sendmessage?';
            $orginator = '99095';
        }
        $data['api_username'] = 'username=' . Auth::user()->username . '';
        $data['api_pass'] = '&password=' . Auth::user()->api_token . '';
        $data['message'] = '&message=testing&phone_number=03211234567';
        $data['orginator'] =  "&orginator=" . $orginator . "";
        return view('admin.users.details', $data);
    }

    public function resellerIndex()
    {
        $data['user'] =  User::where('register_as', 'reseller')->paginate($this->pagination);
        $data['title'] =  'Reseller';
        return view('admin.users.index', $data);
    }

    public function customerIndex($id)
    {
        if (isset($id) && !empty($id)) {
            $id = decrypt($id);
            $data['user']  =  User::findOrFail($id)->getResellerCustomer()->paginate($this->pagination);
        } else {
            $data['user']  =  User::where('register_as', 'customer')->paginate($this->pagination);
        }
        $data['title'] = 'Customer';
        return view('admin.users.index', $data);
    }

    public function create($type = NULL)
    {
        $data['type'] = $type;
        if ($type == NULL) {
            abort(404);
        } elseif ($type == 'masking') {
            $data['maskings'] = Masking::get();
        } else {
            $data['code'] = '99095';
        }
        return view('admin.users.create', $data);
    }

    public function store(Request $request, $type)
    {
        try {
            DB::beginTransaction();

            if ($request->sms > Auth::user()->has_sms && Auth::user()->has_sms == 0) {
                return redirect()->back()->withErrors('Admin have not enough sms');
            }

            $request->validate([
                "first_name"    => "required|string",
                "last_name"     => "required|string",
                "email"         => "required|email|unique:users",
                "password"      => "required|string",
                "cost"          => "required",
                "sms"           => "required",
                "code"          => "sometimes|required|numeric",
                "api_url"       => "sometimes|required",
                'masking.*'     => "sometimes|required",
                'register_as'   => "required|string",
                'invoice_cost'  => "required",
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
                'register_as'       => $request->register_as ?? 'reseller',
                'reference_id'      => Auth::id(),
            ]);

            UsersData::create([
                'user_id'           =>  $user->id,
                'has_sms'           =>  $request->sms,
                'price_per_sms'     =>  $request->cost,
                'Invoice_charges'   =>  $request->invoice_cost,
            ]);

            if ($request->has('masking') && count($request->masking) > 0) {
                foreach ($request->masking as $mask) {
                    UserMasking::create([
                        'user_id'     => $user->id,
                        'masking_id'  => $mask,
                    ]);
                }
            }

            $adminApi = Auth::user()->adminApi;
            $smsApiData['user_id'] = $user->id;
            if ($type == 'masking') {
                $smsApiData['api_url']      = ($request->has('api_url') && !empty($request->api_url)) ? $request->api_url : $adminApi->api_url;
                $smsApiData['api_username'] = ($request->has('api_name') && !empty($request->api_name)) ? $request->api_name : $adminApi->api_username;
                $smsApiData['api_password'] = ($request->has('api_password') && !empty($request->api_password)) ? $request->api_password : $adminApi->api_password;
            } else {
                $smsApiData['api_url']      = $request->api_url ?? NULL;
                $smsApiData['api_username'] = $request->api_name ?? NULL;
                $smsApiData['api_password'] = $request->api_password ?? NULL;
            }
            SmsApi::create($smsApiData);

            Transaction::create([
                'transaction_id' => Auth::id(),
                'admin_id' => Auth::user()->id,
                'user_id' => $user->id,
                'title' => 'transfer sms',
                'description' => 'Transfer sms into new reseller ' . $request->username,
                'amount' =>  $request->sms,
                'type' => 'debit',
                'data' => json_encode(['user_id' => $user->id]),
            ]);

            Transaction::create([
                'transaction_id' => Auth::id(),
                'user_id' => $user->id,
                'admin_id' => Auth::user()->id,
                'title' => 'received sms',
                'description' => 'Received sms by Admin',
                'amount' =>  $request->sms,
                'type' => 'credit',
            ]);

            $adminSmsUpdate = Auth::user()->has_sms - $request->sms;
            Auth::user()->update([
                'has_sms' => $adminSmsUpdate,
            ]);

            DB::commit();
            return redirect()->route('admin.user.' . $user->register_as)->with('success', strtoupper($user->register_as) . ' Created Successfully');
            //code...
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return $e->getMessage();
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail(decrypt($id));
        if ($user->type == 'masking') {
            $data['maskings'] = Masking::get();
        } else {
            $data['code'] = 99095;
        }
        $userMasking = [];
        foreach ($user->getResellerMasking as $masking) {
            array_push($userMasking, $masking->id);
        }
        $data['userMasking']  = $userMasking;
        $data['user'] = $user;
        return view('admin.users.edit', $data);
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

        $data = [
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            // 'username'          => $request->first_name,
            'api_token'         => Str::random('20'),
            'phone_number'      => $request->phone_number,
            'reference_id'      => Auth::id(),
        ];
        if ($request->has('password') && $request->password != NULL) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        $user->UserData()->update([
            'has_sms'           =>  $request->sms,
            'price_per_sms'     =>  $request->cost,
            'Invoice_charges'   =>  $request->invoice_cost,
        ]);

        if ($request->has('masking') && count($request->masking) > 0) {
            foreach ($request->masking as $mask) {
                UserMasking::where('user_id', $user->id)->updateOrCreate([
                    'user_id'     => $user->id,
                    'masking_id'  => $mask,
                ]);
            }
        }

        $adminApi = Auth::user()->adminApi;
        if ($user->type == 'masking') {
            $smsApiData['api_url']      = ($request->has('api_url') && !empty($request->api_url)) ? $request->api_url : $adminApi->api_url;
            $smsApiData['api_username'] = ($request->has('api_name') && !empty($request->api_name)) ? $request->api_name : $adminApi->api_username;
            $smsApiData['api_password'] = ($request->has('api_password') && !empty($request->api_password)) ? $request->api_password : $adminApi->api_password;
        } else {
            $smsApiData['api_url']      = $request->api_url ?? NULL;
            $smsApiData['api_username'] = $request->api_name ?? NULL;
            $smsApiData['api_password'] = $request->api_password ?? NULL;
        }
        SmsApi::where('user_id', $user->id)->update($smsApiData);

        if ($userPervioussms != $request->sms) {
            if ($userPervioussms > $request->sms) {
                $count = $userPervioussms - $request->sms;
                $adminSmsUpdate = Auth::user()->has_sms + $count;
                Transaction::create([
                    'transaction_id' => Auth::id(),
                    'admin_id' => Auth::user()->id,
                    'user_id' => $user->id,
                    'title' => 'Return Sms',
                    'description' => 'Return sms to reseller ' . $request->username,
                    'amount' =>  $count,
                    'type' => 'credit',
                    'data' => json_encode(['user_id' => $user->id]),
                ]);

                Transaction::create([
                    'transaction_id' => Auth::id(),
                    'admin_id' => Auth::user()->id,
                    'user_id' => $user->id,
                    'title' => 'Deduct Sms',
                    'description' => 'Deduct sms by Admin',
                    'amount' =>  $request->sms,
                    'type' => 'debit',
                ]);
            } else {
                $count = $request->sms - $userPervioussms;
                $adminSmsUpdate = Auth::user()->has_sms - $count;

                Transaction::create([
                    'transaction_id' => Auth::id(),
                    'admin_id' => Auth::user()->id,
                    'user_id' => $user->id,
                    'title' => 'transfer sms',
                    'description' => 'Transfer sms into reseller ' . $request->username,
                    'amount' =>  $count,
                    'type' => 'debit',
                    'data' => json_encode(['user_id' => $user->id]),
                ]);

                Transaction::create([
                    'transaction_id' => Auth::id(),
                    'admin_id' => Auth::user()->id,
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
        return redirect()->route('admin.user.' . $user->register_as)->with('success', 'Reseller Updated Successfully');
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail(decrypt($id))->delete();
        return redirect()->back()->with('success', 'User Deleted Successfully');
    }

    public function ResellerCustomer($userId)
    {
        $user = User::findOrFail(decrypt($userId));
        $data['user'] = $user->getResellerCustomer()->paginate($this->pagination);
        return view('admin.users.customer', $data);
    }

    public function userBlocked($id)
    {
        $user = User::findOrFail(decrypt($id));
        $status = ($user->is_blocked == 1) ? '0' : '1';
        $user->update(['is_blocked' => $status]);
        $resason = ($status == 1) ? 'Blocked' : 'UnBlocked';
        return redirect()->back()->with('success', 'User ' . $resason . ' Successfully');
    }
}
