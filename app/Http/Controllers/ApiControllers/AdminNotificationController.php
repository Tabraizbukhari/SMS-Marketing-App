<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class AdminNotificationController extends Controller
{
    public $pagination = 10;

    public function index(Request $request)
    {
        $page = $request->offset??0;
        $limit = $request->limit??$this->pagination;
       
        $users = DB::table('notifications as n')
                    ->Join('users as reseller', 'reseller.id', '=', 'n.user_id')
                    ->Join('users as customer', 'customer.id', '=', 'n.notifiable_id')
                    ->select('n.*', 'reseller.name as reseller_name', 'customer.name as customer_name', 'customer.id as customer_id')
                    ->orderBy('id','desc')
                    ->offset($page)->limit($limit)->get();
                    $notify  = json_decode($users, true);
        $notifications['notificationCount'] = $users = DB::table('notifications')->where('read_at', NULL)->count();
        $dataNotify = [];
        foreach ($notify as $value) {
            $data_message = json_decode($value['data'], true);
            $data = [
                'reseller' => $value['reseller_name'],
                'customer'  => $value['customer_name'],
                'created'   => $value['created_at'],
                'unread'    => $value['read_at'],
                'redirect_url' => route('customer.index',encrypt(['user_id' => $value['customer_id'],'notification_id' =>$value['id']]))
            ];
            array_push($dataNotify, $data);
        }
        $notifications['notification'] = $dataNotify;

        return response()->json($notifications);
    }
}
