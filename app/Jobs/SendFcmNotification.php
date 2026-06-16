<?php

namespace App\Jobs;

use App\Services\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFcmNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        public string $topicOrToken,
        public string $title,
        public string $body,
        public array $data = [],
        public bool $isTopic = true
    ) {}

    public function handle(FirebaseService $service): void
    {
        if ($this->isTopic) {
            $service->sendToTopic($this->topicOrToken, $this->title, $this->body, $this->data);
        } else {
            $service->sendNotification($this->topicOrToken, $this->title, $this->body, $this->data);
        }
    }
}
