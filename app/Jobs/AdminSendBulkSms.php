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

class AdminSendBulkSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messages, $users;
    protected $numbers;

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
            $num = (substr($number, 0, 2) == '03') ? true : ((substr($number, 0, 3) == '923') ? true : ((substr($number, 0, 1) == "3") ? true : false));
            $data['contact_number'] = $number;
            if (strlen((string)$number) >= 10 && strlen((string)$number) <= 12 && $num == true) {

                $htiApi = $this->hitApi($data);
                if (isset($htiApi['Data']['msgid']) && !empty($htiApi['Data']['msgid'])) {
                    $data['price']      =   1;
                    $data['message_id'] = $htiApi['Data']['msgid'];
                    $data['status']     = 'successfully';
                    $sendMessage        = $this->saveMessage($data, $data['campaign_id']);
                    $dataResponse       = 'Campaign run successfully';
                } else {
                    $data['status']     = 'not_sent';
                    $sendMessage        = $this->saveMessage($data, $data['campaign_id']);

                    // return redirect()->back()->withErrors($htiApi['Data']);
                }
            } else {
                $data['message_id'] = NULL;
                $data['status'] = 'not_sent';
                $sendMessage = $this->saveMessage($data, $data['campaign_id']);
            }
        }
        Campaign::find($data['campaign_id'])->update(['status' => 'completed']);
    }



    public function message_url($data)
    {
        $url      = $this->users->adminApi->api_url;
        $username = $this->users->adminApi->api_username;
        $password = $this->users->adminApi->api_password;
        $url .= 'user=' . $username;
        $url .= '&pwd=' . $password;
        $url .= '&sender=' . urlencode($data['orginator']);
        $url .= '&reciever=' . $data['contact_number'];
        $url .= '&msg-data=' . urlencode($data['message']);
        $url .= '&response=json';
        return $url;
    }


    public function hitApi($data)
    {
        $url = $this->message_url($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response =  curl_exec($ch);
        return json_decode($response, true);
    }



    public function saveMessage($data, $campId = NULL)
    {
        $this->AuthSmsCount($data['message_length']);
        $message = Message::create([
            'message_id'        => $data['message_id'],
            'admin_id'          => $this->users->id,
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

        if ($data['api_type'] == 'masking') {
            MessageMasking::create([
                'message_id' => $message->id,
                'masking_id' => $data['masking_id']
            ]);
        }
        if ($campId != NULL) {
            CampaignMessage::create(['message_id' => $message->id, 'campaign_id' => $campId]);
        }
        return $message;
    }
    public function AuthSmsCount($messageLength)
    {
        $total_sms = $this->users->has_sms - $messageLength;
        return $this->users->update(['has_sms' => $total_sms]);
    }
}
