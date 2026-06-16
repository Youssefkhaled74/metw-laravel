<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Database as DatabaseContract;
use Kreait\Firebase\Contract\Messaging as MessagingContract;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected MessagingContract $messaging;
    protected DatabaseContract $database;

    public function __construct(string $context = 'ecommerce')
    {
        if ($context === 'shipment') {
            $credentialsPath = base_path('service-account.json');
            $databaseUri = config('firebase.database.url');
        } else { // ecommerce store
            $credentialsPath = base_path('lasco-store-firebase.json');
            $databaseUri = config('firebase.database.store_url');
        }

        $factory = (new Factory)
            ->withServiceAccount($credentialsPath)
            ->withDatabaseUri($databaseUri);

        $this->database = $factory->createDatabase();
        $this->messaging = $factory->createMessaging();
    }

    public function getDatabase(): DatabaseContract
    {
        return $this->database;
    }

    public function sendNotification(string $token, string $title, string $body, array $data = []): bool
    {
        if (empty($token) || $token === 'faketoken123456789') {
            Log::warning('Invalid FCM token', ['token' => $token]);
            return false;
        }

        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $this->messaging->send($message);
            return true;
        } catch (\Throwable $e) {
            Log::error('Notification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        if (empty($topic)) {
            return false;
        }
        try {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);
            $this->messaging->send($message);
            return true;
        } catch (\Throwable $e) {
            Log::error('Topic notification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
