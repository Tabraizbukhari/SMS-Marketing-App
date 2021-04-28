<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Admin;
use App\Models\User;
use App\Models\Message;
use App\Models\Transaction;
use App\Mail\InvoiceRegisteration;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    public function index()
    {  
        $data['usersCount']         = User::count();
        $data['resellerCount']      = User::where('register_as', 'reseller')->count();
        $data['customerCount']      = User::where('register_as', 'customer')->count();
        $data['successMessage']     = Message::where('status', 'successfully')->count();
        $data['notSentMessage']     = Message::where('status', 'not_sent')->count();
        $data['transfer_sms']       = Transaction::where('admin_id', Auth::id())->where('type','debit')->sum('amount');
        return view('admin.index', $data);
    }



    public function getapi()
    {   
        $data['api_url'] =  'http://sms1.synctechsol.com/api/sendmessage?username='.Auth::user()->username.'&password='.Auth::user()->api_token.'&message=testing&phone_number=090049124&orginator=Masking';
        return view('admin.api.index', $data);
    }

    public function updateLogo(Request $request)
    {
        $request->validate(['logo' => 'required|image']);
        if($request->hasFile('logo')){
            $file = $request->file('logo');
            $file_path = $file->store(Auth::user()->getLogoUrlPath());
            Auth::user()->update([
                'logo_img' => $file_path,
            ]);

            return redirect()->back()->with('success', 'Logo updated successfully');
        }
            return  redirect()->back()->withError('something wents wrong, Try Again');;
    }


    public function transaction()
    {
        $data['transaction'] = Transaction::where('admin_id', Auth::id())->orderBy('id', 'desc')->paginate(10);
        return view('admin.transaction.index', $data);
    }

    public function updateAdminSms(Request $request)
    {
        $request->validate([
            'sms'  => 'required|numeric',
        ]);
        $total = Auth::user()->has_sms + $request->sms;
        $data = Admin::findOrFail(Auth::id())->update(['has_sms' => $total]);
        return redirect()->route('admin.dashboard')->with('success', 'Sms updated successfully');
    }

    public function addAmount(Request $request, $id)
    {
        $request->validate(['amount' => 'required']);
        $user = User::findOrFail(decrypt($id));
        $amount = $user->UserData->has_sms + $request->amount;
      
        Transaction::create([
            'transaction_id' => Auth::id(),
            'admin_id'     => Auth::id(),
            'user_id'      => $user->id,
            'title'       => 'transfer sms',
            'description' => 'Transfer sms into reseller '.$user->username,
            'amount'      =>  $request->amount,
            'type'        => 'debit',
            'data'        => json_encode(['user_id' => $user->id]),
        ]);


        Transaction::create([
            'transaction_id' => $user->id,
            'user_id'     => $user->id,
            // 'admin_id'     => Auth::id(),
            'title'       => 'received sms',
            'description' => 'Received sms by admin',
            'amount'      =>  $request->amount,
            'type'        => 'credit',
            'data'        => json_encode(['user_id' => $user->id]),
        ]);

        $user->UserData()->update([
            'has_sms'   => $amount,
        ]);

        $authSms = Auth::user()->has_sms - $request->amount;
        Auth::user()->update([
            'has_sms'=> $authSms,
        ]);

        return redirect()->back()->with('success', 'Add Amount Successfully');
    }


    public function SendMail()
    {
        $user = Auth::user();
        $data['message']    =  'admin message ';
        $result = Mail::send(new InvoiceRegisteration($user, $data));

        dd($result);
        return redirect()->back()->with('success', 'Mail send successfully');
    }

}
