<?php

namespace App\Jobs;

use App\Models\SmsLog;
use App\Services\Sms\SmsManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendSmsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public int $logId) {}

    public function handle(SmsManager $sms): void
    {
        @set_time_limit(0);

        $log = SmsLog::find($this->logId);
        if (! $log || $log->status !== 'queued') {
            return;
        }

        $sms->deliver($log);
    }
}
