<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Enum\RepresentativeDocumentType;
use App\Enum\RepresentativeStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MetwGo\MetwGoCompleteProfileRequest;
use App\Http\Requests\Api\MetwGo\MetwGoDocumentsRequest;
use App\Http\Requests\Api\MetwGo\MetwGoRegisterRequest;
use App\Http\Requests\Api\MetwGo\MetwGoRegisterStepOneRequest;
use App\Http\Requests\Api\MetwGo\MetwGoRegisterStepTwoRequest;
use App\Http\Requests\Api\MetwGo\MetwGoServiceAreasRequest;
use App\Http\Requests\Api\MetwGo\MetwGoSimpleRegisterRequest;
use App\Http\Requests\Api\MetwGo\MetwGoVehicleRequest;
use App\Models\MediaFile;
use App\Models\Representative;
use App\Models\RepresentativeVehicle;
use App\Models\RepresentativeWorkType;
use App\Models\TransportType;
use App\Models\User;
use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function register(MetwGoRegisterRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $result = DB::transaction(function () use ($validated, $request) {
                $user = User::create([
                    'username' => $validated['first_name'] . ' ' . ($validated['father_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''),
                    'phone' => $validated['phone'],
                    'country_code' => 'EG',
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'phone_verified_at' => now(),
                ]);

                $courierType = $this->metwGoCourierService->mapCourierType($validated['courier_type']);

                $representative = Representative::create([
                    'user_id' => $user->id,
                    'first_name' => $validated['first_name'],
                    'father_name' => $validated['father_name'] ?? null,
                    'last_name' => $validated['last_name'] ?? null,
                    'phone' => $validated['phone'],
                    'second_phone' => $validated['secondary_phone'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'address' => $validated['address_details'] ?? null,
                    'account_type' => $courierType,
                    'warehouse_id' => $validated['warehouse_id'] ?? null,
                    'status' => RepresentativeStatus::PENDING_REVIEW,
                    'submitted_at' => now(),
                    'village_service' => $validated['villages_service_enabled'],
                ]);

                $mappedWorkTypes = $this->metwGoCourierService->mapWorkTypes($validated['work_types']);

                foreach ($mappedWorkTypes as $workType) {
                    RepresentativeWorkType::create([
                        'representative_id' => $representative->id,
                        'work_type' => $workType,
                    ]);
                }

                $this->metwGoCourierService->validateStepFourRules(
                    $validated['work_types'],
                    $validated['governorate_ids'],
                    $validated['city_ids'] ?? []
                );

                $representative->governorates()->sync($validated['governorate_ids']);

                if (!empty($validated['city_ids'])) {
                    $representative->cities()->sync($validated['city_ids']);
                }

                $transportType = TransportType::findOrFail($validated['transport_type_id']);

                RepresentativeVehicle::create([
                    'representative_id' => $representative->id,
                    'transport_type_id' => $validated['transport_type_id'],
                    'registration_number' => $validated['plate_number'] ?? null,
                    'max_weight' => $transportType->max_weight,
                    'max_volume' => $transportType->max_volume,
                ]);

                if ($request->hasFile('vehicle_image')) {
                    $path = uploadImage($request, 'vehicle_image', 'storage/metwgo/vehicles');

                    if ($path) {
                        $this->metwGoCourierService->storeVehicleImage($representative, $path);
                    }
                }

                $documentMapping = [
                    'profile_photo' => RepresentativeDocumentType::PERSONAL_PHOTO,
                    'national_id_front' => RepresentativeDocumentType::NATIONAL_ID_FRONT,
                    'national_id_back' => RepresentativeDocumentType::NATIONAL_ID_BACK,
                    'driving_license_front' => RepresentativeDocumentType::DRIVING_LICENSE_FRONT,
                    'driving_license_back' => RepresentativeDocumentType::DRIVING_LICENSE_BACK,
                    'vehicle_license_front' => RepresentativeDocumentType::VEHICLE_LICENSE_FRONT,
                    'vehicle_license_back' => RepresentativeDocumentType::VEHICLE_LICENSE_BACK,
                ];

                foreach ($documentMapping as $field => $docType) {
                    if ($request->hasFile($field)) {
                        $path = uploadImage($request, $field, 'storage/metwgo/documents');

                        if ($path) {
                            MediaFile::create([
                                'mediable_type' => Representative::class,
                                'mediable_id' => $representative->id,
                                'collection_name' => 'representative_documents',
                                'document_type' => $docType->value,
                                'url' => $path,
                                'mime_type' => $request->file($field)->getMimeType(),
                                'size' => $request->file($field)->getSize(),
                            ]);
                        }
                    }
                }

                $accessToken = $this->metwGoCourierService->createAccessToken($user);

                $representative->load([
                    'user',
                    'warehouse',
                    'workTypes.option',
                    'governorates',
                    'cities',
                    'vehicle.transportType',
                    'mediaFiles',
                ]);

                return [
                    'access_token' => $accessToken,
                    'courier' => $this->metwGoCourierService->formatCourier($representative),
                    'profile' => $this->metwGoCourierService->formatRepresentativeProfile($representative),
                ];
            });

            return responseJson(true, 'تم التسجيل بنجاح', $result, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return responseJson(false, $e->getMessage(), $e->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function stepOne(MetwGoRegisterStepOneRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $result = DB::transaction(function () use ($validated) {
                $user = User::create([
                    'username' => $validated['first_name'] . ' ' . ($validated['father_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''),
                    'phone' => $validated['phone'],
                    'country_code' => 'EG',
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'phone_verified_at' => now(),
                ]);

                $representative = Representative::create([
                    'user_id' => $user->id,
                    'first_name' => $validated['first_name'],
                    'father_name' => $validated['father_name'] ?? null,
                    'last_name' => $validated['last_name'] ?? null,
                    'phone' => $validated['phone'],
                    'second_phone' => $validated['secondary_phone'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'address' => $validated['address_details'] ?? null,
                    'status' => RepresentativeStatus::INCOMPLETE,
                ]);

                $registrationToken = $this->metwGoCourierService->createRegistrationToken($user);

                return [
                    'registration_token' => $registrationToken,
                    'current_step' => 2,
                ];
            });

            return responseJson(true, 'تم حفظ المعلومات الشخصية', $result, 201);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function stepTwo(MetwGoRegisterStepTwoRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            DB::transaction(function () use ($representative, $validated) {
                $courierType = $this->metwGoCourierService->mapCourierType($validated['courier_type']);

                $representative->update([
                    'account_type' => $courierType,
                    'warehouse_id' => $validated['warehouse_id'] ?? null,
                ]);

                $representative->workTypes()->delete();
                $mappedWorkTypes = $this->metwGoCourierService->mapWorkTypes($validated['work_types']);

                foreach ($mappedWorkTypes as $workType) {
                    RepresentativeWorkType::create([
                        'representative_id' => $representative->id,
                        'work_type' => $workType,
                    ]);
                }
            });

            return responseJson(true, 'تم حفظ معلومات المندوب', [
                'registration_token' => $this->metwGoCourierService->createRegistrationToken($user),
                'current_step' => 3,
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function stepThree(MetwGoVehicleRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            $transportType = TransportType::findOrFail($validated['transport_type_id']);

            DB::transaction(function () use ($representative, $validated, $transportType, $request) {
                RepresentativeVehicle::updateOrCreate(
                    ['representative_id' => $representative->id],
                    [
                        'transport_type_id' => $validated['transport_type_id'],
                        'registration_number' => $validated['plate_number'] ?? null,
                        'max_weight' => $transportType->max_weight,
                        'max_volume' => $transportType->max_volume,
                    ]
                );

                if ($request->hasFile('vehicle_image')) {
                    $path = uploadImage($request, 'vehicle_image', 'storage/metwgo/vehicles');
                    if ($path) {
                        $this->metwGoCourierService->storeVehicleImage($representative, $path);
                    }
                }
            });

            return responseJson(true, 'تم حفظ معلومات المركبة', [
                'registration_token' => $this->metwGoCourierService->createRegistrationToken($user),
                'current_step' => 4,
                'max_weight_kg' => (float) $transportType->max_weight,
                'max_volume_m3' => (float) $transportType->max_volume,
            ], 201);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function stepFour(MetwGoServiceAreasRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            $workTypes = $representative->workTypes->pluck('work_type')->all();
            $mappedWorkTypes = $this->metwGoCourierService->unmapWorkTypes($representative->workTypes);

            $this->metwGoCourierService->validateStepFourRules(
                $mappedWorkTypes,
                $validated['governorate_ids'],
                $validated['city_ids'] ?? []
            );

            DB::transaction(function () use ($representative, $validated) {
                $representative->governorates()->sync($validated['governorate_ids']);

                if (!empty($validated['city_ids'])) {
                    $representative->cities()->sync($validated['city_ids']);
                } else {
                    $representative->cities()->sync([]);
                }

                $representative->update([
                    'village_service' => $validated['villages_service_enabled'],
                ]);
            });

            return responseJson(true, 'تم حفظ مناطق الخدمة', [
                'registration_token' => $this->metwGoCourierService->createRegistrationToken($user),
                'current_step' => 5,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return responseJson(false, $e->getMessage(), $e->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function stepFive(MetwGoDocumentsRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            DB::transaction(function () use ($representative, $request) {
                $documentMapping = [
                    'profile_photo' => RepresentativeDocumentType::PERSONAL_PHOTO,
                    'national_id_front' => RepresentativeDocumentType::NATIONAL_ID_FRONT,
                    'national_id_back' => RepresentativeDocumentType::NATIONAL_ID_BACK,
                    'driving_license_front' => RepresentativeDocumentType::DRIVING_LICENSE_FRONT,
                    'driving_license_back' => RepresentativeDocumentType::DRIVING_LICENSE_BACK,
                    'vehicle_license_front' => RepresentativeDocumentType::VEHICLE_LICENSE_FRONT,
                    'vehicle_license_back' => RepresentativeDocumentType::VEHICLE_LICENSE_BACK,
                ];

                foreach ($documentMapping as $field => $docType) {
                    if ($request->hasFile($field)) {
                        $path = uploadImage($request, $field, 'storage/metwgo/documents');

                        if ($path) {
                            MediaFile::create([
                                'mediable_type' => Representative::class,
                                'mediable_id' => $representative->id,
                                'collection_name' => 'representative_documents',
                                'document_type' => $docType->value,
                                'url' => $path,
                                'mime_type' => $request->file($field)->getMimeType(),
                                'size' => $request->file($field)->getSize(),
                            ]);
                        }
                    }
                }

                $representative->update([
                    'status' => RepresentativeStatus::PENDING_REVIEW,
                    'submitted_at' => now(),
                ]);
            });

            return responseJson(true, 'تم رفع المستندات وإرسال الحساب للمراجعة', [
                'approval_status' => 'pending_approval',
            ], 201);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function simpleRegister(MetwGoSimpleRegisterRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $result = DB::transaction(function () use ($validated) {
                $user = User::create([
                    'username' => $validated['first_name'] . ' ' . ($validated['father_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''),
                    'phone' => $validated['phone'],
                    'country_code' => 'EG',
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'phone_verified_at' => now(),
                ]);

                $representative = Representative::create([
                    'user_id' => $user->id,
                    'first_name' => $validated['first_name'],
                    'father_name' => $validated['father_name'] ?? null,
                    'last_name' => $validated['last_name'] ?? null,
                    'phone' => $validated['phone'],
                    'status' => RepresentativeStatus::PENDING_APPROVAL,
                    'submitted_at' => now(),
                    'is_profile_complete' => false,
                ]);

                $accessToken = $this->metwGoCourierService->createAccessToken($user);

                return [
                    'access_token' => $accessToken,
                    'token_type' => 'Bearer',
                ];
            });

            return responseJson(true, 'تم التسجيل بنجاح. سيتم مراجعة حسابك من قبل الإدارة.', $result, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return responseJson(false, $e->getMessage(), $e->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function completeProfile(MetwGoCompleteProfileRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            if ($representative->is_profile_complete) {
                return responseJson(false, 'تم إكمال الملف الشخصي مسبقاً.', null, 422);
            }

            $isActive = in_array($representative->status?->value ?? $representative->status, [
                RepresentativeStatus::ACTIVE->value,
                RepresentativeStatus::APPROVED->value,
            ]);

            if (!$isActive) {
                return responseJson(false, 'يجب الموافقة على الحساب أولاً قبل إكمال الملف الشخصي.', null, 403);
            }

            $result = DB::transaction(function () use ($representative, $validated, $request) {
                $courierType = $this->metwGoCourierService->mapCourierType($validated['courier_type']);

                $representative->update([
                    'account_type' => $courierType,
                    'warehouse_id' => $validated['warehouse_id'] ?? null,
                    'village_service' => $validated['villages_service_enabled'],
                ]);

                $mappedWorkTypes = $this->metwGoCourierService->mapWorkTypes($validated['work_types']);

                foreach ($mappedWorkTypes as $workType) {
                    RepresentativeWorkType::create([
                        'representative_id' => $representative->id,
                        'work_type' => $workType,
                    ]);
                }

                $this->metwGoCourierService->validateStepFourRules(
                    $validated['work_types'],
                    $validated['governorate_ids'],
                    $validated['city_ids'] ?? []
                );

                $representative->governorates()->sync($validated['governorate_ids']);

                if (!empty($validated['city_ids'])) {
                    $representative->cities()->sync($validated['city_ids']);
                }

                $transportType = TransportType::findOrFail($validated['transport_type_id']);

                RepresentativeVehicle::create([
                    'representative_id' => $representative->id,
                    'transport_type_id' => $validated['transport_type_id'],
                    'registration_number' => $validated['plate_number'] ?? null,
                    'max_weight' => $transportType->max_weight,
                    'max_volume' => $transportType->max_volume,
                ]);

                if ($request->hasFile('vehicle_image')) {
                    $path = uploadImage($request, 'vehicle_image', 'storage/metwgo/vehicles');

                    if ($path) {
                        $this->metwGoCourierService->storeVehicleImage($representative, $path);
                    }
                }

                $documentMapping = [
                    'profile_photo' => RepresentativeDocumentType::PERSONAL_PHOTO,
                    'national_id_front' => RepresentativeDocumentType::NATIONAL_ID_FRONT,
                    'national_id_back' => RepresentativeDocumentType::NATIONAL_ID_BACK,
                    'driving_license_front' => RepresentativeDocumentType::DRIVING_LICENSE_FRONT,
                    'driving_license_back' => RepresentativeDocumentType::DRIVING_LICENSE_BACK,
                    'vehicle_license_front' => RepresentativeDocumentType::VEHICLE_LICENSE_FRONT,
                    'vehicle_license_back' => RepresentativeDocumentType::VEHICLE_LICENSE_BACK,
                ];

                foreach ($documentMapping as $field => $docType) {
                    if ($request->hasFile($field)) {
                        $path = uploadImage($request, $field, 'storage/metwgo/documents');

                        if ($path) {
                            MediaFile::create([
                                'mediable_type' => Representative::class,
                                'mediable_id' => $representative->id,
                                'collection_name' => 'representative_documents',
                                'document_type' => $docType->value,
                                'url' => $path,
                                'mime_type' => $request->file($field)->getMimeType(),
                                'size' => $request->file($field)->getSize(),
                            ]);
                        }
                    }
                }

                $representative->update([
                    'is_profile_complete' => true,
                ]);

                $representative->load([
                    'user',
                    'warehouse',
                    'workTypes.option',
                    'governorates',
                    'cities',
                    'vehicle.transportType',
                    'mediaFiles',
                ]);

                return [
                    'profile' => $this->metwGoCourierService->formatRepresentativeProfile($representative),
                    'approval_status' => 'pending_approval',
                ];
            });

            return responseJson(true, 'تم إكمال الملف الشخصي بنجاح', $result, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return responseJson(false, $e->getMessage(), $e->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function registrationStatus(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            $status = $this->metwGoCourierService->approvalStatus($representative);

            $messages = [
                'incomplete' => 'لم يتم إكمال التسجيل بعد.',
                'pending_approval' => 'حسابك قيد المراجعة، وسنبلغك بمجرد الموافقة عليه وجاهزيته للتفعيل.',
                'approved' => 'تم الموافقة على حسابك.',
                'rejected' => 'تم رفض الحساب. الرجاء التواصل مع الدعم.',
                'suspended' => 'تم إيقاف الحساب. الرجاء التواصل مع الدعم.',
            ];

            return responseJson(true, $messages[$status] ?? '', [
                'status' => $status,
                'message' => $messages[$status] ?? '',
                'next_screen' => match ($status) {
                    'pending_approval' => 'waiting_approval',
                    'approved' => 'home',
                    'incomplete' => 'sign_up',
                    default => 'login',
                },
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'لم يتم العثور على حساب مندوب.', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
