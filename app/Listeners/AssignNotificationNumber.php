<?php

namespace App\Listeners;

use App\Services\NotificationNumberService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Events\NotificationSent;

class AssignNotificationNumber
{
    public function __construct(
        protected NotificationNumberService $notificationNumberService
    ) {}

    public function handle(NotificationSent $event): void
    {
        if ($event->channel !== 'database' || ! $event->response instanceof Model) {
            return;
        }

        if (! empty($event->response->notification_number)) {
            return;
        }

        $event->response->forceFill([
            'notification_number' => $this->notificationNumberService->generate(),
        ])->save();
    }
}
