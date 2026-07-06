<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NotificationNumberService
{
    public function generate(): string
    {
        return DB::transaction(function () {
            $last = DB::table('notifications')
                ->whereNotNull('notification_number')
                ->lockForUpdate()
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->value('notification_number');

            $nextNumber = 1;

            if ($last) {
                preg_match('/(\d+)$/', (string) $last, $matches);
                $nextNumber = ((int) ($matches[1] ?? 0)) + 1;
            }

            return 'NOT-' . str_pad((string) $nextNumber, 8, '0', STR_PAD_LEFT);
        });
    }
}
