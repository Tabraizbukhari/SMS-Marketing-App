<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use Carbon\Carbon;
use App\Models\Masking;
use Auth;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\SmsApi;

class SmsCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sma:check-pending-sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all users of pending sms';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public $sms_api_url ;
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $messages = Message::where('send_date', '>=', Carbon::now())->get();
        foreach ($messages as $msg) {
            $data = [
                'masking_name' => Masking::findOrFail($msg->masking_id)->title,
                'contact_number' => $msg->contact_number,
                'message'        => $msg->message,
            ];
            $sms_api = SmsApi::where('user_id',Auth::id())->first();
            $url = $sms_api['api_url'];
            $url .= 'user='.$sms_api['api_username'];
            $url .= '&pwd='.$sms_api['api_password'];
            $url .= '&sender='.Masking::findOrFail($msg->masking_id)->title;
            $url .= '&reciever='.$msg->contact_number;
            $url .= '&msg-data='.$msg->message;
            $url .= '&response=json';

           
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result=  curl_exec($ch);
            $error = (isset($result))? json_decode($result): null;
            if(isset($error) && $error != null){
                if(!isset($error->Data->status)){
                    $this->info($error->Data);
                }
            }
            $hitapi  = json_decode($result, true);
            $message = Message::findOrFail($msg->id);
            $message->getCampaign()->update(['status' => 'completed']);
            $message->update(['status' => 'successfully', 'message_id' => $hitapi['Data']['msgid']]);
        }
        
        $this->info('Command run successfully');          
    }

}
