<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\UsersData;
use App\Models\Admin;
use Auth;

class SendCreatedSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data, $users; 
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $users)
    {
            $this->data = $data;
            $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
        $userid =$this->users['id'];
        $userData = UsersData::where('user_id', $userid)->first();

        if($userData->has_sms >= $data['message_length']){
            $sms = $userData->has_sms - $data['message_length'];
            $userData->update(['has_sms' => $sms]);
            return   $this->fail('condition ');
        
        }else{
            return   $this->fail('false');

        }
        if($userData->has_sms >= $data['message_length']){
            $htiApi = $this->hitApi($data);
            if($this->users->type == 'masking'){
                if(isset($htiApi['Data']['msgid']) && !empty($htiApi['Data']['msgid'])){            
                    $sms = $userData->has_sms - $data['message_length'];
                    $userData->update(['has_sms' => $sms]);
                
                    $data['message_id'] = $htiApi['Data']['msgid'];
                    $update = ['status' => 'successfully', 'message_id' => $data['message_id'] ];
                    Message::findOrFail($data['mid'])->update($update);
                }else{
                    return   $this->fail($htiApi['Data']);
                }
            }else{
                if(isset($htiApi['data']) && isset($htiApi['data']['acceptreport']['messageid']) && $htiApi['action'] == "sendmessage"){
                    $sms = $userData->has_sms - $data['message_length'];
                    $userData->update(['has_sms' => $sms]);

                    $data['message_id'] = $htiApi['data']['acceptreport']['messageid'];
                    $data['message_id'] = $htiApi['Data']['msgid'];
                    $update = ['status' => 'successfully', 'message_id' => $data['message_id'] ];
                    Message::findOrFail($data['mid'])->update($update);
                }else if(isset($htiApi['action']) && $htiApi['action'] == "error"){
                    return   $this->fail($htiApi['data']['errormessage']);
                }
            }
        }else{
            $failmessage = $userid.' has enough sms to run job';
            return   $this->fail($failmessage);
        }
    }


    public function message_url($data){   
        $admin = Admin::first();
        $url      = $this->users->getUserSmsApi['api_url']??$admin->adminApi->api_url;
        $username = $this->users->getUserSmsApi['api_username']??$admin->adminApi->api_username;
        $password = $this->users->getUserSmsApi['api_password']??$admin->adminApi->api_password;
        if($this->users->type == 'masking'){
            $url .= 'user='.$username;
            $url .= '&pwd='.$password;
            $url .= '&sender='.urlencode($data['orginator']);
            $url .= '&reciever='.$data['contact_number'];
            $url .= '&msg-data='.urlencode($data['message']);
            $url .= '&response=json';
        }else{
            $url .= 'action=sendmessage';
            $url .= '&username='.$this->users->getUserSmsApi->api_username;
            $url .= '&password='.$this->users->getUserSmsApi->api_password;
            $url .= '&recipient='.$data['contact_number'];
            $url .= '&originator='.$data['orginator'];
            $url .= '&messagedata='.urlencode($data['message']);
            $url .= '&sendondate='.urlencode(date('Y-m-d h:m:s', strtotime($data['send_date'])));
            $url .= '&responseformat=xml';
        }
        return $url;
    }


    public function hitApi($data)
    {
        $url = $this->message_url($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response =  curl_exec($ch);

        if($this->users->type == 'masking'){
            return json_decode($response, true);
        }
        $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        return $array = json_decode($json,TRUE);
        
    }

  

   
}
