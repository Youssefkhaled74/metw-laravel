<?php

namespace App\Jobs;

use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5; // seconds

    public function __construct(
        public string $to,
        public string $subject,
        public string $htmlBody,
        public string $fromName = 'Lasco'
    ) {}

    public function handle(EmailService $emailService): void
    {
        $emailService->send($this->to, $this->subject, $this->htmlBody, $this->fromName);
    }
}
