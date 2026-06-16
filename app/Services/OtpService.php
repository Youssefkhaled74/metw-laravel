<?php

namespace App\Services;

use App\Enum\OtpPurpose;
use App\Models\OtpCode;
use App\Models\User;
use Carbon\Carbon;

class OtpService
{
    public function createAndReturnCode(
        User $user,
        OtpPurpose $purpose,
        int $length = 4,
        int $ttlMinutes = 5,
        bool $invalidatePrevious = true
    ): string {
        if ($invalidatePrevious) {
            OtpCode::where('user_id', $user->id)
                ->where('purpose', $purpose)
                ->where('is_used', false)
                ->update(['is_used' => true]);
        }

        $code = $this->generateNumericCode($length);

        OtpCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'purpose' => $purpose,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes($ttlMinutes),
        ]);

        return $code;
    }

    private function generateNumericCode(int $length): string
    {
        $min = (int) str_pad('1', $length, '0');
        $max = (int) str_pad('', $length, '9');
        return (string) random_int($min, $max);
    }
}
