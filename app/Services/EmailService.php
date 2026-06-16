<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function send(string $to, string $subject, string $htmlBody): bool
    {
        try {
            $payload = [
                'to' => $to,
                'subject' => $subject,
                'body' => $htmlBody,
                'from_name' => 'Lasco',
            ];

            $response = Http::retry(3, 200)->timeout(10)
                ->post('https://mailapi.evyx.net/api/send-email', $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('Email API failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'to' => $to,
            ]);
            return false;
        } catch (\Throwable $th) {
            Log::error('Exception while sending email', [
                'error' => $th->getMessage(),
                'to' => $to,
            ]);
            return false;
        }
    }
}
