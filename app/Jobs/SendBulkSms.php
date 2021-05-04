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
use App\Models\UsersData;
use App\Jobs\SendCreatedSMS;

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
            $messageleng = $this->stringCount($data['message']);
            if(strlen((string)$number) >= 10 && strlen((string)$number) <= 12 && $num == true){
                if(substr($number, 0, 3) == '033' || substr($number, 0, 2) == '33' || substr($number, 0, 4) == '9233'){
                    $messageleng = $messageleng * 1.5;
                }

                $data['message_length'] =   $messageleng;
                $data['price']      = $this->users->UserData->price_per_sms * $data['message_length'];
                $data['message_id'] = NULL;
                $data['status'] = 'pending';
                $sendMessage    = $this->saveMessage($data, $data['campaign_id']);
                $dataResponse = 'Campaign run successfully';
                $data['mid'] = $sendMessage->id;
                // $jobs = (new SendCreatedSMS($data, $this->users))->delay(now()->addSeconds(1));
                // dispatch($jobs);
                
            }else{
                $data['message_id'] = NULL;
                $data['status'] = 'not_sent';
                $sendMessage = $this->saveMessage($data, $data['campaign_id']);
            }
        }
        Campaign::find($data['campaign_id'])->update(['status','completed']);
    }

    public function stringCount($message){
        $count = '';
        if (strlen($message) != strlen(utf8_decode($message))){
            $urduCount = strlen(utf8_decode($message));
            switch ($urduCount) {
                case $urduCount <= 70:
                        $count = 1;
                    break;
                case $urduCount <= 134:
                        $count = 2;
                    break;
                case $urduCount <= 201:
                        $count = 3;
                    break;  
                case $urduCount <= 268:
                        $count = 4;
                    break;  
                case $urduCount <= 355:
                        $count = 5;
                    break;  
                default:
                    $count = false;
                    break;
            }

        }else{
            $englishCount  = strlen($message);
            switch ($englishCount) {
                case $englishCount <= 160:
                        $count = 1;
                    break;
                case $englishCount <= 320:
                        $count = 2;
                    break;
                case $englishCount <= 480:
                        $count = 3;
                    break;  
                case $englishCount <= 640:
                        $count = 4;
                    break;  
                case $englishCount <= 800:
                        $count = 5;
                    break;  
                default:
                    $count = false;
                    break;
            }
        }

        return $count;
    }

  

    public function saveMessage($data, $campId = NULL){
        // $this->AuthSmsCount($data['message_length']);
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
        
        // if($data['status'] == 'successfully'){
        //     $userData = UsersData::where('user_id', $this->users->id)->first();
        //     $sms = $userData->has_sms - $data['message_length'];
        //     $userData->update(['has_sms' => $sms]);
        // }

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
    // public function AuthSmsCount($messageLength)
    // {
    //     $total_sms = $this->users->UserData->has_sms - $messageLength;
    //     return $this->users->UserData()->update(['has_sms' => $total_sms]);
    // }

}
