<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Enum\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MetwGo\MetwGoLoginRequest;
use App\Http\Requests\Api\MetwGo\MetwGoOtpRequest;
use App\Http\Requests\Api\MetwGo\MetwGoPasswordResetRequest;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\MetwGo\MetwGoCourierService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private const OTP_EXPIRES_IN_SECONDS = 300;

    private const OTP_RESEND_AFTER_SECONDS = 20;

    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function login(MetwGoLoginRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::where('phone', $validated['phone'])->first();

            if (! $user || ! Hash::check($validated['password'], $user->password)) {
                return $this->authError(
                    'بيانات تسجيل الدخول غير صحيحة.',
                    401,
                    'login',
                    'invalid_credentials'
                );
            }

            $representative = $user->representative;

            if (! $representative) {
                return $this->authError(
                    'لم يتم العثور على حساب مندوب.',
                    404,
                    'login',
                    'courier_not_found'
                );
            }

            $approvalStatus = $this->metwGoCourierService->approvalStatus($representative);

            if ($approvalStatus === 'pending_approval') {
                return $this->authError(
                    'حسابك قيد المراجعة، وسيتم تفعيل الدخول بعد الموافقة عليه.',
                    403,
                    'waiting_approval',
                    'pending_approval',
                    [
                        'status' => 'pending_approval',
                        'next_action' => 'wait_for_approval',
                    ]
                );
            }

            if (in_array($approvalStatus, ['rejected', 'suspended'], true)) {
                $message = $approvalStatus === 'suspended'
                    ? 'تم إيقاف الحساب. الرجاء التواصل مع الدعم.'
                    : 'تم رفض الحساب. الرجاء التواصل مع الدعم.';

                return $this->authError(
                    $message,
                    403,
                    'login',
                    $approvalStatus,
                    [
                        'status' => $approvalStatus,
                        'next_action' => 'contact_support',
                    ]
                );
            }

            if ($approvalStatus !== 'approved') {
                return $this->authError(
                    'حسابك غير مكتمل. يرجى إكمال التسجيل أولاً.',
                    403,
                    'login',
                    'registration_incomplete',
                    [
                        'status' => $approvalStatus,
                        'next_action' => 'complete_registration',
                    ]
                );
            }

            if (! empty($validated['device_token'])) {
                $user->update(['fcm_token' => $validated['device_token']]);
            }

            $accessToken = $this->metwGoCourierService->createAccessToken($user);
            $courier = $this->metwGoCourierService->formatCourier($representative);

            return responseJson(true, 'تم تسجيل الدخول بنجاح', [
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'courier' => $courier,
                'next_screen' => 'home',
            ], 200);
        } catch (\Throwable $th) {
            return $this->authError(
                'حدث خطأ غير متوقع أثناء تسجيل الدخول.',
                500,
                'login',
                'login_failed'
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return responseJson(true, 'تم تسجيل الخروج بنجاح', [
                'next_screen' => 'login',
            ]);
        } catch (\Throwable $th) {
            return $this->authError(
                'حدث خطأ غير متوقع أثناء تسجيل الخروج.',
                500,
                'logout',
                'logout_failed'
            );
        }
    }

    public function sendForgotPasswordOtp(Request $request): JsonResponse
    {
        try {
            $request->validate(['phone' => ['required', 'regex:/^01[0-2,5]{1}[0-9]{8}$/']]);

            $user = User::where('phone', $request->phone)->first();

            if (! $user) {
                return $this->authError(
                    'رقم الهاتف غير مسجل.',
                    404,
                    'forgot_password',
                    'phone_not_found'
                );
            }

            $cooldown = $this->metwGoCourierService->otpCooldownSeconds($user, 'forgot_password');

            if ($cooldown > 0) {
                return $this->authError(
                    'يرجى الانتظار قبل طلب رمز جديد.',
                    429,
                    'forgot_password',
                    'otp_cooldown',
                    [
                        'retry_after_seconds' => $cooldown,
                    ]
                );
            }

            $this->createOtp($user, OtpPurpose::PASSWORD_RESET);
            $maskedPhone = $this->metwGoCourierService->maskPhone($user->phone);

            return responseJson(true, 'تم إرسال رمز التحقق', [
                'masked_phone' => $maskedPhone,
                'expires_in_seconds' => self::OTP_EXPIRES_IN_SECONDS,
                'resend_after_seconds' => self::OTP_RESEND_AFTER_SECONDS,
                'next_screen' => 'otp',
            ], 200);
        } catch (\Throwable $th) {
            return $this->authError(
                'حدث خطأ غير متوقع أثناء إرسال رمز التحقق.',
                500,
                'forgot_password',
                'otp_send_failed'
            );
        }
    }

    public function verifyOtp(MetwGoOtpRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::where('phone', $validated['phone'])->first();

            if (! $user) {
                return $this->authError(
                    'رقم الهاتف غير مسجل.',
                    404,
                    'otp',
                    'phone_not_found'
                );
            }

            $purpose = match ($validated['purpose'] ?? 'forgot_password') {
                'forgot_password' => OtpPurpose::PASSWORD_RESET,
                'phone_verification' => OtpPurpose::PHONE_VERIFICATION,
                'registration' => OtpPurpose::REGISTER,
                default => OtpPurpose::PASSWORD_RESET,
            };

            $isValid = $this->validateOtp($user, $validated['otp'], $purpose);

            if (! $isValid) {
                return $this->authError(
                    'رمز التحقق غير صالح أو منتهي الصلاحية.',
                    422,
                    'otp',
                    'otp_invalid'
                );
            }

            $data = [
                'purpose' => $validated['purpose'] ?? 'forgot_password',
                'next_screen' => $purpose === OtpPurpose::PASSWORD_RESET ? 'reset_password' : null,
            ];

            if ($purpose === OtpPurpose::PASSWORD_RESET) {
                $data['reset_token'] = $this->metwGoCourierService->createResetToken($user);
            }

            $this->markOtpUsed($user, $purpose);

            return responseJson(true, 'تم التحقق من الرمز بنجاح', $data, 200);
        } catch (\Throwable $th) {
            return $this->authError(
                'حدث خطأ غير متوقع أثناء التحقق من الرمز.',
                500,
                'otp',
                'otp_verify_failed'
            );
        }
    }

    public function resendOtp(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'phone' => ['required', 'regex:/^01[0-2,5]{1}[0-9]{8}$/'],
                'purpose' => ['sometimes', 'required', 'string'],
            ]);

            $user = User::where('phone', $request->phone)->first();

            if (! $user) {
                return $this->authError(
                    'رقم الهاتف غير مسجل.',
                    404,
                    'otp',
                    'phone_not_found'
                );
            }

            $purpose = match ($request->input('purpose', 'forgot_password')) {
                'forgot_password' => OtpPurpose::PASSWORD_RESET,
                'phone_verification' => OtpPurpose::PHONE_VERIFICATION,
                'registration' => OtpPurpose::REGISTER,
                default => OtpPurpose::PASSWORD_RESET,
            };

            $cooldown = $this->metwGoCourierService->otpCooldownSeconds($user, $purpose->value);

            if ($cooldown > 0) {
                return $this->authError(
                    'يرجى الانتظار قبل إعادة إرسال الرمز.',
                    429,
                    'otp',
                    'otp_cooldown',
                    [
                        'retry_after_seconds' => $cooldown,
                    ]
                );
            }

            $this->createOtp($user, $purpose);

            return responseJson(true, 'تم إعادة إرسال الرمز بنجاح', [
                'expires_in_seconds' => self::OTP_EXPIRES_IN_SECONDS,
                'resend_after_seconds' => self::OTP_RESEND_AFTER_SECONDS,
            ], 200);
        } catch (\Throwable $th) {
            return $this->authError(
                'حدث خطأ غير متوقع أثناء إعادة إرسال الرمز.',
                500,
                'otp',
                'otp_resend_failed'
            );
        }
    }

    public function resetPassword(MetwGoPasswordResetRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::where('phone', $validated['phone'])->first();

            if (! $user) {
                return $this->authError(
                    'رقم الهاتف غير مسجل.',
                    404,
                    'reset_password',
                    'phone_not_found'
                );
            }

            $isValid = $this->metwGoCourierService->validateResetToken($user, $validated['reset_token']);

            if (! $isValid) {
                return $this->authError(
                    'رمز إعادة تعيين كلمة المرور غير صالح أو منتهي الصلاحية.',
                    422,
                    'reset_password',
                    'reset_token_invalid'
                );
            }

            $user->update(['password' => $validated['password']]);
            $this->metwGoCourierService->clearResetToken($user);

            return responseJson(true, 'تم تغيير كلمة المرور بنجاح', [
                'next_screen' => 'login',
            ], 200);
        } catch (\Throwable $th) {
            return $this->authError(
                'حدث خطأ غير متوقع أثناء تغيير كلمة المرور.',
                500,
                'reset_password',
                'password_reset_failed'
            );
        }
    }

    public function changePassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user = $request->user();
            $user->update(['password' => $request->password]);

            return responseJson(true, 'تم تغيير كلمة المرور بنجاح', null, 200);
        } catch (\Throwable $th) {
            return $this->authError(
                'حدث خطأ غير متوقع أثناء تغيير كلمة المرور.',
                500,
                'change_password',
                'password_change_failed'
            );
        }
    }

    private function createOtp(User $user, OtpPurpose $purpose): void
    {
        OtpCode::create([
            'user_id' => $user->id,
            'code' => '1111',
            'purpose' => $purpose,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes(5),
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

        return $otp && $otp->code == $code;
    }

    private function markOtpUsed(User $user, OtpPurpose $purpose): void
    {
        OtpCode::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->update(['is_used' => true]);
    }

    private function authError(
        string $message,
        int $status,
        string $screen,
        string $code,
        array $data = []
    ): JsonResponse {
        return responseJson(false, $message, array_merge([
            'screen' => $screen,
            'code' => $code,
        ], $data), $status);
    }
}
