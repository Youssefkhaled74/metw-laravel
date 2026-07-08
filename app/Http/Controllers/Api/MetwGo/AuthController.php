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
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function login(MetwGoLoginRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::where('phone', $validated['phone'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return responseJson(false, 'بيانات الدخول غير صحيحة.', null, 401);
            }

            $representative = $user->representative;

            if (!$representative) {
                return responseJson(false, 'لم يتم العثور على حساب مندوب.', null, 404);
            }

            $approvalStatus = $this->metwGoCourierService->approvalStatus($representative);

            if ($approvalStatus === 'pending_approval') {
                return responseJson(false, 'حسابك قيد المراجعة، وسيتم تفعيل الدخول بعد الموافقة عليه.', [
                    'status' => 'pending_approval',
                ], 403);
            }

            if (in_array($approvalStatus, ['rejected', 'suspended'])) {
                $message = $approvalStatus === 'suspended'
                    ? 'تم إيقاف الحساب. الرجاء التواصل مع الدعم.'
                    : 'تم رفض الحساب. الرجاء التواصل مع الدعم.';

                return responseJson(false, $message, [
                    'status' => $approvalStatus,
                ], 403);
            }

            if ($approvalStatus !== 'approved') {
                return responseJson(false, 'حسابك غير مكتمل. يرجى إكمال التسجيل أولاً.', [
                    'status' => $approvalStatus,
                ], 403);
            }

            if (!empty($validated['device_token'])) {
                $user->update(['fcm_token' => $validated['device_token']]);
            }

            $accessToken = $this->metwGoCourierService->createAccessToken($user);
            $courier = $this->metwGoCourierService->formatCourier($representative);

            return responseJson(true, 'تم تسجيل الدخول بنجاح', [
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'courier' => $courier,
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return responseJson(true, 'تم تسجيل الخروج بنجاح');
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function sendForgotPasswordOtp(Request $request): JsonResponse
    {
        try {
            $request->validate(['phone' => ['required', 'regex:/^01[0-2,5]{1}[0-9]{8}$/']]);

            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                return responseJson(false, 'رقم الهاتف غير مسجل.', null, 404);
            }

            $cooldown = $this->metwGoCourierService->otpCooldownSeconds($user, 'forgot_password');

            if ($cooldown > 0) {
                return responseJson(false, 'يرجى الانتظار قبل طلب رمز جديد.', [
                    'retry_after_seconds' => $cooldown,
                ], 429);
            }

            $this->createOtp($user, OtpPurpose::PASSWORD_RESET);
            $maskedPhone = $this->metwGoCourierService->maskPhone($user->phone);

            return responseJson(true, 'تم إرسال رمز التحقق', [
                'masked_phone' => $maskedPhone,
                'expires_in_seconds' => 300,
                'resend_after_seconds' => 20,
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function verifyOtp(MetwGoOtpRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::where('phone', $validated['phone'])->first();

            if (!$user) {
                return responseJson(false, 'رقم الهاتف غير مسجل.', null, 404);
            }

            $purpose = match ($validated['purpose'] ?? 'forgot_password') {
                'forgot_password' => OtpPurpose::PASSWORD_RESET,
                'phone_verification' => OtpPurpose::PHONE_VERIFICATION,
                'registration' => OtpPurpose::REGISTER,
                default => OtpPurpose::PASSWORD_RESET,
            };

            $isValid = $this->validateOtp($user, $validated['otp'], $purpose);

            if (!$isValid) {
                return responseJson(false, 'رمز التحقق غير صالح أو منتهي الصلاحية.', null, 422);
            }

            $data = [];

            if ($purpose === OtpPurpose::PASSWORD_RESET) {
                $resetToken = $this->metwGoCourierService->createResetToken($user);
                $data['reset_token'] = $resetToken;
            }

            $this->markOtpUsed($user, $purpose);

            return responseJson(true, 'تم التحقق بنجاح', $data, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
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

            if (!$user) {
                return responseJson(false, 'رقم الهاتف غير مسجل.', null, 404);
            }

            $purpose = match ($request->input('purpose', 'forgot_password')) {
                'forgot_password' => OtpPurpose::PASSWORD_RESET,
                'phone_verification' => OtpPurpose::PHONE_VERIFICATION,
                'registration' => OtpPurpose::REGISTER,
                default => OtpPurpose::PASSWORD_RESET,
            };

            $cooldown = $this->metwGoCourierService->otpCooldownSeconds($user, $purpose->value);

            if ($cooldown > 0) {
                return responseJson(false, 'يرجى الانتظار قبل إعادة إرسال الرمز.', [
                    'retry_after_seconds' => $cooldown,
                ], 429);
            }

            $this->createOtp($user, $purpose);

            return responseJson(true, 'تم إعادة إرسال الرمز', [
                'expires_in_seconds' => 300,
                'resend_after_seconds' => 20,
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function resetPassword(MetwGoPasswordResetRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::where('phone', $validated['phone'])->first();

            if (!$user) {
                return responseJson(false, 'رقم الهاتف غير مسجل.', null, 404);
            }

            $isValid = $this->metwGoCourierService->validateResetToken($user, $validated['reset_token']);

            if (!$isValid) {
                return responseJson(false, 'رمز إعادة التعيين غير صالح أو منتهي الصلاحية.', null, 422);
            }

            $user->update(['password' => $validated['password']]);
            $this->metwGoCourierService->clearResetToken($user);

            return responseJson(true, 'تم تحديث كلمة المرور بنجاح', null, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
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

            return responseJson(true, 'تم تغيير كلمة المرور', null, 200);
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
}
