<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Response;
use Carbon\Carbon;
use App\Models\Message;

class VerifiedCodeSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sma:verified-code-sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
            $file_url= 'https://reporting_smsc41.eocean.us/download_json.php?username=synctech&password=R2zuFkmYwXgCApRj&date=2021-04-19';
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            $read = readfile($file_url); 
            $data = json_decode($read, true);
            foreach ((array) $data as $d) {
                if(Message::where('message_id', $d['MsgID'])->exists()){
                    if($d['status'] == 'DTH'){  
                        Message::where('message_id', $d['MsgID'])->update(['is_verified', '1']);  
                    }              
            }
            return true;
        }
    }
}
