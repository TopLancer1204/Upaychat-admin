<?php

namespace App\Jobs;

use App\Helper\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $to;
    public $message;
    public $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $message, $type) //type-> 0: twilio, 1: multitexter
    {
        $this->to = $to;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Helper::sendSMS($this->to, $this->message, $this->type);
    }
}
