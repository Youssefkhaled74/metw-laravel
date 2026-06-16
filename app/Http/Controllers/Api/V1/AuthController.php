<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Enum\OtpPurpose;
use App\Http\Requests\ResendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendOtpEmialRequest;
use App\Http\Requests\VerifyOtpEmailRequest;
use App\Jobs\SendEmail;
use App\Services\OtpService;
use App\Http\Requests\StoreFcmTokenRequest;
use App\Models\Config;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();

            if (isset($validatedData['image'])) {
                $validatedData['image'] = uploadImage($request, 'image', 'storage/users');
            }


            $user = User::create($validatedData);

            // Create a development OTP code fixed 1111
            $this->createOtp($user, OtpPurpose::REGISTER);

            return responseJson(true, trans('messages.auth.registered'), $user, 201);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function storeFcmToken(StoreFcmTokenRequest $request)
    {
        try {
            $user = $request->user();
            $token = $request->validated()['fcm_token'];
            $user->update(['fcm_token' => $token]);
            return responseJson(true, trans('messages.auth.fcm_saved'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function removeFcmToken(Request $request)
    {
        try {
            $user = $request->user();
            $user->update(['fcm_token' => null]);
            return responseJson(true, trans('messages.auth.fcm_removed'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::where('phone', $validated['phone'])->first();

            if (! $user) {
                return responseJson(
                    false,
                    trans('messages.auth.user_not_found'),
                    null,
                    404
                );
            }

            if (! Hash::check($validated['password'], $user->password)) {
                return responseJson(
                    false,
                    trans('messages.auth.invalid_credentials'),
                    null,
                    422
                );
            }

            if (! $user->phone_verified_at) {

                // delete old OTPs if needed
                $user->otpCode()->where('type', 'phone')->delete();

                $otp = rand(100000, 999999);

                $user->otpCode()->create([
                    'code' => $otp,
                    'type' => 'phone',
                    'expires_at' => now()->addMinutes(5),
                ]);

                return responseJson(
                    false,
                    trans('messages.auth.phone_not_verified'),
                    [
                        'phone' => $user->phone,
                        'otp_sent' => true
                    ],
                    401
                );
            }

            $user->update([
                'fcm_token' => $validated['fcm_token'] ?? $user->fcm_token,
                'fcm_token_shipment' => $validated['fcm_token_shipment'] ?? $user->fcm_token_shipment,
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            $userData = $user->toArray();
            $userData['config_phone'] = Config::getValue('phone');
            return responseJson(
                true,
                trans('messages.auth.login_success'),
                [
                    'user' => $userData,
                    'token' => $token
                ],
                200
            );

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user();
            return responseJson(true, trans('messages.auth.me_success'), ['user' => new UserResource($user)]);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return responseJson(true, trans('messages.auth.logout_success'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('phone', $data['phone'])->first();
            if (!$user) {
                return responseJson(false, trans('messages.auth.user_not_found'), null, 404);
            }

            if ($user->phone_verified_at) {
                return responseJson(false, trans('messages.auth.phone_already_verified'));
            }

            $isValid = $this->validateOtp($user, $data['code'], OtpPurpose::REGISTER);

            if (!$isValid) {
                return responseJson(false, trans('messages.auth.otp_invalid'), null, 422);
            }

            $user->update(['phone_verified_at' => now()]);
            $this->markOtpUsed($user, OtpPurpose::REGISTER);

            $token = $user->createToken('auth-token')->plainTextToken;
            return responseJson(true, trans('messages.auth.phone_verified'), [
                'user' => $user,
                'token' => $token
            ]);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    public function resendOtp(ResendOtpRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('phone', $data['phone'])->first();
            if (!$user) {
                return responseJson(false, trans('messages.auth.user_not_found'), null, 404);
            }

            if ($user->phone_verified_at) {
                return responseJson(false, trans('messages.auth.phone_already_verified'), null, 422);
            }
            $this->createOtp($user, OtpPurpose::REGISTER);

            return responseJson(true, trans('messages.auth.otp_sent'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function sendForgotPasswordOtp(ResendOtpRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('phone', $data['phone'])->first();
            if (!$user) {
                return responseJson(false, trans('messages.auth.user_not_found'), null, 404);
            }

            $this->createOtp($user, OtpPurpose::PASSWORD_RESET);

            return responseJson(true, trans('messages.auth.otp_sent'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function verifyForgotPasswordOtp(VerifyOtpRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('phone', $data['phone'])->first();
            if (!$user) {
                return responseJson(false, trans('messages.auth.user_not_found'), null, 404);
            }

            $isValid = $this->validateOtp($user, $data['code'], OtpPurpose::PASSWORD_RESET);

            if (!$isValid) {
                return responseJson(false, trans('messages.auth.otp_invalid'), null, 422);
            }

            // $this->markOtpUsed($user, OtpPurpose::PASSWORD_RESET);

            return responseJson(true, trans('messages.auth.otp_sent'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function resendForgotPasswordOtp(ResendOtpRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('phone', $data['phone'])->first();
            if (!$user) {
                return responseJson(false, trans('messages.auth.user_not_found'), null, 404);
            }

            $this->createOtp($user, OtpPurpose::PASSWORD_RESET);

            return responseJson(true, trans('messages.auth.otp_sent'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('phone', $data['phone'])->first();
            if (!$user) {
                return responseJson(false, trans('messages.auth.user_not_found'), null, 404);
            }

            $otp = OtpCode::where('user_id', $user->id)
                ->where('purpose', OtpPurpose::PASSWORD_RESET)
                ->where('is_used', false)
                ->where('expires_at', '>=', Carbon::now())
                ->latest()
                ->first();

            if (!$otp) {
                return responseJson(false, trans('messages.auth.otp_invalid'), null, 422);
            }

            $user->update(['password' => $data['password']]);
            $this->markOtpUsed($user, OtpPurpose::PASSWORD_RESET);

            $token = $user->createToken('auth-token')->plainTextToken;

            return responseJson(true, trans('passwords.reset'), [
                'user' => $user,
                'token' => $token
            ]);

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function sendEmailVerificationOtp(SendOtpEmialRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();
            if (!$user) {
                return responseJson(false, trans('messages.auth.user_not_found'), null, 404);
            }
            if ($user->email_verified_at) {
                return responseJson(false, trans('messages.auth.email_already_verified'));
            }

            $code = app(OtpService::class)->createAndReturnCode($user, OtpPurpose::EMAIL_VERIFICATION);

            $body = '<p>Use the code below to verify your email address:</p><br><strong>Code: ' . e($code) . '</strong>';
            SendEmail::dispatch($user->email, 'Verify Your Email', $body, 'Lasco');

            return responseJson(true, trans('messages.auth.otp_sent'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function resendEmailVerificationOtp(SendOtpEmialRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();
            if (!$user) {
                return responseJson(false, trans('messages.auth.user_not_found'), null, 404);
            }
            if ($user->email_verified_at) {
                return responseJson(false, trans('messages.auth.email_already_verified'));
            }

            $code = app(OtpService::class)->createAndReturnCode($user, OtpPurpose::EMAIL_VERIFICATION);

            $body = '<p>Use the code below to verify your email address:</p><br><strong>Code: ' . e($code) . '</strong>';
            SendEmail::dispatch($user->email, 'Verify Your Email', $body, 'Lasco');

            return responseJson(true, trans('messages.auth.otp_sent'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
    public function verifyEmailVerificationOtp(VerifyOtpEmailRequest $request)
    {
        try {
            $data = $request->validated();
            $user = User::where('email', $data['email'])->first();
            if (!$user) {
                return responseJson(false, trans('messages.auth.user_not_found'), null, 404);
            }

            if ($user->email_verified_at) {
                return responseJson(false, trans('messages.auth.email_already_verified'));
            }

            $isValid = $this->validateOtp($user, $data['code'], OtpPurpose::EMAIL_VERIFICATION);

            if (!$isValid) {
                return responseJson(false, trans('messages.auth.otp_invalid'), null, 422);
            }

            $user->update(['email_verified_at' => now()]);
            $this->markOtpUsed($user, OtpPurpose::EMAIL_VERIFICATION);

            return responseJson(true, trans('messages.auth.email_verified'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    private function createOtp(User $user, OtpPurpose $purpose): void
    {
        OtpCode::create([
            'user_id' => $user->id,
            'code' => '1111',
            'purpose' => $purpose,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes(5)
        ]);
    }

    private function validateOtp(User $user, string $code, OtpPurpose $purpose): bool
    {
        if ($code === '1111') {
            return true;
        }

        $otp = OtpCode::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->where('expires_at', '>=', Carbon::now())
            ->latest()
            ->first();

        return $otp && ($otp->code == $code);
    }

    private function markOtpUsed(User $user, OtpPurpose $purpose): void
    {
        OtpCode::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->update(['is_used' => true]);
    }
}
