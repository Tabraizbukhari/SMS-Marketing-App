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
        })->paginate($this->pagination);
        $data['user'] = $user;
        return view('dashboard.reseller.index', $data);
    }

    public function create()
    {
        $data['maskings'] = Masking::get();
        return view('dashboard.reseller.create', $data);
    }

    public function store(Request $request)
    {
        if(Auth::user()->sms == 0){
            return redirect()->back()->withErrors('Admin have no more sms');
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

        if($request->has('masking')){
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
        
        if($request->has('api_name') && $request->has('api_password')){
            SmsApi::create([
                'user_id'       => $user->id,
                'api_url'       => $this->api_url,
                'api_username'  => $request->api_name,
                'api_password'  => $request->api_password,
            ]);
        }
      
        return redirect()->route('admin.reseller.index')->with('success','Reseller Created Successfully');
    }
    

    public function destroy($id)
    {
        $user = User::findOrFail(decrypt($id));
        $user->delete();
        return redirect()->back()->with('success','Reseller deleted Successfully');
    }
}
