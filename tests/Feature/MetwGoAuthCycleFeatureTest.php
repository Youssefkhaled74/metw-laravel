<?php

namespace Tests\Feature;

use App\Enum\RepresentativeStatus;
use App\Models\Representative;
use App\Models\TransportType;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\TransportTypeSeeder;
use Database\Seeders\WarehouseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MetwGoAuthCycleFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        File::ensureDirectoryExists(public_path('storage/metwgo/documents'));
        File::ensureDirectoryExists(public_path('storage/metwgo/vehicles'));

        $this->seed([
            WarehouseSeeder::class,
            TransportTypeSeeder::class,
        ]);
    }

    public function test_metwgo_step_registration_to_approval_login_change_password_and_logout_cycle(): void
    {
        $warehouse = Warehouse::query()->firstOrFail();
        $transportType = TransportType::query()->where('code', '07')->firstOrFail();
        $governorate = $warehouse->governorate()->firstOrFail();
        $city = $warehouse->city()->firstOrFail();

        $stepOneResponse = $this->postJson('/api/metwgo/auth/register/step-1', [
            'first_name' => 'Ahmed',
            'father_name' => 'Mahmoud',
            'last_name' => 'Ali',
            'phone' => '01012345678',
            'secondary_phone' => '01012345679',
            'email' => 'ahmed.metwgo@example.com',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'address_details' => 'Nasr City, Cairo',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $stepOneResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.current_step', 2);

        $registrationToken = $stepOneResponse->json('data.registration_token');

        $this->assertNotEmpty($registrationToken);

        $statusAfterStepOne = $this
            ->withToken($registrationToken)
            ->getJson('/api/metwgo/auth/registration/status');

        $statusAfterStepOne
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'incomplete')
            ->assertJsonPath('data.next_screen', 'sign_up');

        $stepTwoResponse = $this
            ->withToken($registrationToken)
            ->postJson('/api/metwgo/auth/register/step-2', [
                'courier_type' => 'freelance',
                'work_types' => [
                    'delivery_inside_governorate',
                    'shipping_between_governorates',
                    'bus_driver_between_governorates',
                ],
                'warehouse_id' => $warehouse->id,
            ]);

        $stepTwoResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.current_step', 3);

        $registrationToken = $stepTwoResponse->json('data.registration_token');

        $stepThreeResponse = $this
            ->withToken($registrationToken)
            ->post('/api/metwgo/auth/register/step-3', [
                'transport_type_id' => $transportType->id,
                'plate_number' => 'ABC-1234',
                'vehicle_image' => UploadedFile::fake()->image('vehicle.png'),
            ]);

        $stepThreeResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.current_step', 4)
            ->assertJsonPath('data.max_weight_kg', 700);

        $registrationToken = $stepThreeResponse->json('data.registration_token');

        $stepFourResponse = $this
            ->withToken($registrationToken)
            ->postJson('/api/metwgo/auth/register/step-4', [
                'governorate_ids' => [$governorate->id],
                'city_ids' => [$city->id],
                'villages_service_enabled' => true,
            ]);

        $stepFourResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.current_step', 5);

        $registrationToken = $stepFourResponse->json('data.registration_token');

        $stepFiveResponse = $this
            ->withToken($registrationToken)
            ->post('/api/metwgo/auth/register/step-5', [
                'profile_photo' => UploadedFile::fake()->image('profile.jpg'),
                'national_id_front' => UploadedFile::fake()->image('nid-front.jpg'),
                'national_id_back' => UploadedFile::fake()->image('nid-back.jpg'),
                'driving_license_front' => UploadedFile::fake()->image('dl-front.jpg'),
                'driving_license_back' => UploadedFile::fake()->image('dl-back.jpg'),
                'vehicle_license_front' => UploadedFile::fake()->image('vl-front.jpg'),
                'vehicle_license_back' => UploadedFile::fake()->image('vl-back.jpg'),
            ]);

        $stepFiveResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.approval_status', 'pending_approval');

        $statusAfterSubmission = $this
            ->withToken($registrationToken)
            ->getJson('/api/metwgo/auth/registration/status');

        $statusAfterSubmission
            ->assertOk()
            ->assertJsonPath('data.status', 'pending_approval')
            ->assertJsonPath('data.next_screen', 'waiting_approval');

        $pendingLoginResponse = $this->postJson('/api/metwgo/auth/login', [
            'phone' => '01012345678',
            'password' => 'Password@123',
        ]);

        $pendingLoginResponse
            ->assertForbidden()
            ->assertJsonPath('success', false)
            ->assertJsonPath('data.code', 'pending_approval')
            ->assertJsonPath('data.next_action', 'wait_for_approval');

        $representative = Representative::query()
            ->where('phone', '01012345678')
            ->firstOrFail();

        $representative->update([
            'status' => RepresentativeStatus::APPROVED,
            'approved_at' => now(),
            'reviewed_at' => now(),
        ]);

        $approvedLoginResponse = $this->postJson('/api/metwgo/auth/login', [
            'phone' => '01012345678',
            'password' => 'Password@123',
            'device_token' => 'device-token-123',
        ]);

        $approvedLoginResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.next_screen', 'home')
            ->assertJsonPath('data.courier.approval_status', 'approved');

        $accessToken = $approvedLoginResponse->json('data.access_token');

        $changePasswordResponse = $this
            ->withToken($accessToken)
            ->postJson('/api/metwgo/auth/password/change', [
                'password' => 'NewPassword@123',
                'password_confirmation' => 'NewPassword@123',
            ]);

        $changePasswordResponse
            ->assertOk()
            ->assertJsonPath('success', true);

        $logoutResponse = $this
            ->withToken($accessToken)
            ->postJson('/api/metwgo/auth/logout');

        $logoutResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.next_screen', 'login');

        $reLoginResponse = $this->postJson('/api/metwgo/auth/login', [
            'phone' => '01012345678',
            'password' => 'NewPassword@123',
        ]);

        $reLoginResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.next_screen', 'home');
    }

    public function test_metwgo_forgot_password_otp_reset_cycle(): void
    {
        $user = User::create([
            'username' => 'courier.auth',
            'phone' => '01055554444',
            'country_code' => 'EG',
            'email' => 'courier.auth@example.com',
            'password' => 'Password@123',
            'phone_verified_at' => now(),
        ]);

        Representative::create([
            'user_id' => $user->id,
            'first_name' => 'Courier',
            'father_name' => 'Test',
            'last_name' => 'User',
            'phone' => '01055554444',
            'status' => RepresentativeStatus::APPROVED,
            'approved_at' => now(),
            'reviewed_at' => now(),
            'is_active' => true,
            'village_service' => false,
        ]);

        $sendOtpResponse = $this->postJson('/api/metwgo/auth/forgot-password/send-otp', [
            'phone' => '01055554444',
        ]);

        $sendOtpResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.next_screen', 'otp')
            ->assertJsonPath('data.expires_in_seconds', 300);

        $verifyOtpResponse = $this->postJson('/api/metwgo/auth/otp/verify', [
            'phone' => '01055554444',
            'otp' => '1111',
            'purpose' => 'forgot_password',
        ]);

        $verifyOtpResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.next_screen', 'reset_password');

        $resetToken = $verifyOtpResponse->json('data.reset_token');

        $this->assertNotEmpty($resetToken);

        $resetPasswordResponse = $this->postJson('/api/metwgo/auth/password/reset', [
            'phone' => '01055554444',
            'reset_token' => $resetToken,
            'password' => 'ResetPassword@123',
            'password_confirmation' => 'ResetPassword@123',
        ]);

        $resetPasswordResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.next_screen', 'login');

        $loginResponse = $this->postJson('/api/metwgo/auth/login', [
            'phone' => '01055554444',
            'password' => 'ResetPassword@123',
        ]);

        $loginResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.next_screen', 'home');
    }
}
