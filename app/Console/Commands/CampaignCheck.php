<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\BulkSmsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Message;
use Auth;
use App\Models\Masking;

class CampaignCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sma:check-pending-campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all pending campaign of sms';

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
        $this->info('messages');
    }

}
