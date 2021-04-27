<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\MessageMasking;
use App\Models\CampaignMessage;
use App\Models\Campaign;
use Auth;
use App\Models\Admin;


class SendBulkSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $messages, $users;
    protected $numbers;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $numbers)
    {
        $this->users = Auth::user();
        $this->messages = $data;
        $this->numbers = $numbers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->messages;
        $numbers = $this->numbers;
        foreach ($numbers as $number) {
            $num = (substr($number, 0, 2) == '03')? true : ((substr($number, 0, 3) == '923')? true : ((substr($number, 0, 1) == "3")? true:false) );
            $data['contact_number'] = $number;

            if(substr($number, 0, 3) == '033' || substr($number, 0, 4) == '9233'){
                $data['price'] += $data['message_length'] / 2 + $data['message_length'];
             }else{
                $data['price'] = $this->users->UserData->price_per_sms * $data['message_length']; 
             }

            if(strlen((string)$number) >= 10 && strlen((string)$number) <= 12 && $num == true){
                
                $htiApi = $this->hitApi($data);
                if($this->users->type == 'masking'){
                    if(isset($htiApi['Data']['msgid']) && !empty($htiApi['Data']['msgid'])){
                        $data['message_id'] = $htiApi['Data']['msgid'];
                        $data['status']     = 'successfully';
                        $sendMessage        = $this->saveMessage($data, $data['campaign_id']);
                        $dataResponse       = 'Campaign run successfully';

                    }else{
                        $data['status']     = 'not_sent';
                        $sendMessage        = $this->saveMessage($data, $data['campaign_id']);

                        // return redirect()->back()->withErrors($htiApi['Data']);
                    }
                }else{
                    if(isset($htiApi['data']) && isset($htiApi['data']['acceptreport']['messageid']) && $htiApi['action'] == "sendmessage"){
                        $data['message_id'] = $htiApi['data']['acceptreport']['messageid'];
                        $data['status'] = 'successfully';
                        $sendMessage        = $this->saveMessage($data, $data['campaign_id']);
                        $dataResponse = 'Campaign run successfully';
                    }else if(isset($htiApi['action']) && $htiApi['action'] == "error"){
                        $data['status'] = 'not_sent';
                        $sendMessage    = $this->saveMessage($data, $data['campaign_id']);
                        Campaign::find($data['campaign_id'])->update(['status','failed']);
                        // return redirect()->back()->withErrors($htiApi['data']['errormessage']);
                    }else{
                        $data['status'] = 'not_sent';
                        $sendMessage    = $this->saveMessage($data, $data['campaign_id']);
                        Campaign::find($data['campaign_id'])->update(['status','failed']);
                        // return redirect()->back()->withErrors('Something wents wrong! plesae contact your admistrator');
                    }
                }
            }else{
                $data['message_id'] = NULL;
                $data['status'] = 'not_sent';
                $sendMessage = $this->saveMessage($data, $data['campaign_id']);
            }
        }
        Campaign::find($data['campaign_id'])->update(['status','successfully']);
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

  

    public function saveMessage($data, $campId = NULL){
        $this->AuthSmsCount($data['message_length']);
        $message = Message::create([
            'message_id'        => $data['message_id'],
            'user_id'           => $this->users->id,
            'message'           => $data['message'],
            'message_length'    => $data['message_length'],
            'contact_number'    => $data['contact_number'],
            'send_date'         => $data['send_date'],
            'type'              => $data['type'],
            'price'             => $data['price'],
            'api_type'          => $data['api_type'],
            'status'            => $data['status'],
            'reference'         => $data['orginator']
        ]);

        if($data['api_type'] == 'masking'){
            MessageMasking::create([
                'message_id' => $message->id,
                'masking_id' => $data['masking_id']
            ]);
        }
        if($campId != NULL){
            CampaignMessage::create(['message_id' => $message->id, 'campaign_id' => $campId]);
        }
        return $message;
    }
    public function AuthSmsCount($messageLength)
    {
        $total_sms = $this->users->UserData->has_sms - $messageLength;
        return $this->users->UserData()->update(['has_sms' => $total_sms]);
    }

}
