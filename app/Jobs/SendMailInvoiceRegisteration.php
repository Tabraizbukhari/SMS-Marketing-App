<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\InvoiceRegisteration;
use Illuminate\Support\Facades\Mail;

class SendMailInvoiceRegisteration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user, $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user , $data)
    {
        $this->user  = $user;
        $this->data  = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user  ;
        $data = $this->data  ;
        $result = Mail::send(new InvoiceRegisteration($user, $data));
    }
}
