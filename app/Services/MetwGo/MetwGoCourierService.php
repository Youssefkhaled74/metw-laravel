<?php

namespace App\Services\MetwGo;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeStatus;
use App\Enum\ShipmentRequestStatus;
use App\Models\City;
use App\Models\OrderItem;
use App\Models\OtpCode;
use App\Models\Representative;
use App\Models\RepresentativeWorkTypeOption;
use App\Models\ShipmentRequest;
use App\Models\TransportType;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MetwGoCourierService
{
    public const REGISTRATION_TOKEN_NAME = 'metwgo-registration';

    public const ACCESS_TOKEN_NAME = 'metwgo-access';

    public function getRepresentativeForUser(User $user): Representative
    {
        return $user->representative()
            ->with([
                'user',
                'warehouse',
                'workTypes.option',
                'governorates',
                'cities',
                'vehicle.transportType',
                'mediaFiles',
            ])
            ->firstOrFail();
    }

    public function approvalStatus(Representative $representative): string
    {
        return match ($representative->status?->value ?? $representative->status) {
            RepresentativeStatus::APPROVED->value => 'approved',
            RepresentativeStatus::ACTIVE->value => 'approved',
            RepresentativeStatus::PENDING_REVIEW->value => 'pending_approval',
            RepresentativeStatus::PENDING_APPROVAL->value => 'pending_approval',
            RepresentativeStatus::REJECTED->value => 'rejected',
            RepresentativeStatus::SUSPENDED->value => 'suspended',
            RepresentativeStatus::INACTIVE->value => 'suspended',
            default => 'incomplete',
        };
    }

    public function assertApproved(Representative $representative): void
    {
        $status = $this->approvalStatus($representative);

        if ($status !== 'approved') {
            throw ValidationException::withMessages([
                'approval_status' => [$status],
            ]);
        }
    }

    public function createRegistrationToken(User $user): string
    {
        $user->tokens()
            ->where('name', self::REGISTRATION_TOKEN_NAME)
            ->delete();

        return $user->createToken(self::REGISTRATION_TOKEN_NAME)->plainTextToken;
    }

    public function createAccessToken(User $user): string
    {
        return $user->createToken(self::ACCESS_TOKEN_NAME)->plainTextToken;
    }

    public function mapCourierType(?string $value): ?string
    {
        return match ($value) {
            'freelance' => RepresentativeAccountType::FREE->value,
            'warehouse' => RepresentativeAccountType::WAREHOUSE->value,
            default => $value,
        };
    }

    public function unmapCourierType(?string $value): ?string
    {
        return match ($value) {
            RepresentativeAccountType::FREE->value => 'freelance',
            RepresentativeAccountType::WAREHOUSE->value => 'warehouse',
            default => $value,
        };
    }

    public function mapWorkTypes(array $workTypes): array
    {
        return collect($workTypes)
            ->filter()
            ->map(function ($workType) {
                return match ($workType) {
                    'delivery_inside_governorate' => 'local_delivery',
                    'shipping_between_governorates' => 'inter_governorate_shipping',
                    'bus_driver_between_governorates' => 'bus_driver',
                    default => $workType,
                };
            })
            ->values()
            ->all();
    }

    public function unmapWorkTypes(Collection $workTypes): array
    {
        return $workTypes->map(function ($workType) {
            $code = is_string($workType) ? $workType : $workType->work_type;

            return match ($code) {
                'local_delivery' => 'delivery_inside_governorate',
                'inter_governorate_shipping' => 'shipping_between_governorates',
                'bus_driver' => 'bus_driver_between_governorates',
                default => $code,
            };
        })->values()->all();
    }

    public function workTypeValidationCodes(): array
    {
        return [
            'delivery_inside_governorate',
            'shipping_between_governorates',
            'bus_driver_between_governorates',
        ];
    }

    public function representativeSelectableWorkTypes(): array
    {
        return RepresentativeWorkTypeOption::activeCodes();
    }

    public function maskPhone(string $phone): string
    {
        if (mb_strlen($phone) <= 4) {
            return $phone;
        }

        return mb_substr($phone, 0, 3) . '******' . mb_substr($phone, -2);
    }

    public function otpCooldownSeconds(User $user, string $purpose): int
    {
        $lastOtp = OtpCode::query()
            ->where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->latest()
            ->first();

        if (! $lastOtp) {
            return 0;
        }

        $cooldown = 20 - now()->diffInSeconds($lastOtp->created_at);

        return max(0, $cooldown);
    }

    public function setAvailability(Representative $representative, string $status): Representative
    {
        $metadata = $representative->metadata ?? [];
        $metadata['availability_status'] = $status;

        if ($status === 'online') {
            $metadata['online_started_at'] = now()->toIso8601String();
        } else {
            $metadata['online_started_at'] = null;
        }

        $representative->update(['metadata' => $metadata]);

        return $representative->fresh([
            'user',
            'warehouse',
            'workTypes.option',
            'governorates',
            'cities',
            'vehicle.transportType',
            'mediaFiles',
        ]);
    }

    public function availabilityStatus(Representative $representative): string
    {
        return data_get($representative->metadata, 'availability_status', 'offline');
    }

    public function onlineStartedAt(Representative $representative): ?string
    {
        return data_get($representative->metadata, 'online_started_at');
    }

    public function onlineDurationSeconds(Representative $representative): int
    {
        $startedAt = $this->onlineStartedAt($representative);

        if (! $startedAt) {
            return 0;
        }

        return Carbon::parse($startedAt)->diffInSeconds(now());
    }

    public function activeOrderQuery(Representative $representative): Builder
    {
        return OrderItem::query()
            ->with([
                'order',
                'package.packageDetails',
                'package.pickupAddress',
                'package.dropoffAddress',
                'route',
            ])
            ->where('representative_id', $representative->id)
            ->whereIn('status', ['accepted', 'pickup', 'on_way']);
    }

    public function incomingOrdersQuery(Representative $representative): Builder
    {
        $query = OrderItem::query()
            ->with([
                'order',
                'package.packageDetails',
                'package.pickupAddress',
                'package.dropoffAddress',
                'route',
            ])
            ->whereNull('representative_id')
            ->where('status', 'pending');

        $workTypes = $representative->workTypes->pluck('work_type')->values();

        if ($workTypes->contains('local_delivery')) {
            $governorateIds = $representative->governorates->pluck('id');
            $cityIds = $representative->cities->pluck('id');

            $query->whereHas('package.pickupAddress', function (Builder $builder) use ($governorateIds, $cityIds) {
                $builder->whereIn('governorate_id', $governorateIds);

                if ($cityIds->isNotEmpty()) {
                    $builder->whereIn('city_id', $cityIds);
                }
            });
        } elseif ($representative->governorates->isNotEmpty()) {
            $governorateIds = $representative->governorates->pluck('id');

            $query->whereHas('package.pickupAddress', function (Builder $builder) use ($governorateIds) {
                $builder->whereIn('governorate_id', $governorateIds);
            });
        }

        if (! ($representative->village_service ?? false)) {
            $query->whereDoesntHave('package.dropoffAddress', function (Builder $builder) {
                $builder->where('district_or_village_type', 'village');
            });
        }

        if ($representative->vehicle?->transportType) {
            $maxWeight = (float) ($representative->vehicle->transportType->max_weight ?? $representative->vehicle->max_weight ?? 0);

            if ($maxWeight > 0) {
                $query->whereHas('package', function (Builder $builder) use ($maxWeight) {
                    $builder->where(function (Builder $subQuery) use ($maxWeight) {
                        $subQuery->whereNull('weight')
                            ->orWhere('weight', '<=', $maxWeight);
                    });
                });
            }
        }

        return $query->latest();
    }

    public function formatCourier(Representative $representative): array
    {
        $fullName = trim(collect([
            $representative->first_name,
            $representative->father_name,
            $representative->last_name,
        ])->filter()->implode(' '));

        $profilePhoto = $representative->mediaFiles
            ->where('collection_name', 'representative_documents')
            ->firstWhere('document_type', 'personal_photo');

        return [
            'id' => $representative->id,
            'account_number' => str_replace('REP-', 'CRR-', (string) $representative->account_number),
            'name' => $fullName,
            'phone' => $representative->phone,
            'approval_status' => $this->approvalStatus($representative),
            'avatar' => $profilePhoto?->url ?? ($representative->user?->image ? asset($representative->user->image) : null),
            'rating' => (float) data_get($representative->metadata, 'rating', 4.9),
            'availability_status' => $this->availabilityStatus($representative),
            'online_duration_seconds' => $this->onlineDurationSeconds($representative),
        ];
    }

    public function formatRepresentativeProfile(Representative $representative): array
    {
        return [
            'id' => $representative->id,
            'account_number' => str_replace('REP-', 'CRR-', (string) $representative->account_number),
            'first_name' => $representative->first_name,
            'father_name' => $representative->father_name,
            'last_name' => $representative->last_name,
            'phone' => $representative->phone,
            'secondary_phone' => $representative->second_phone,
            'email' => $representative->user?->email,
            'birth_date' => optional($representative->birth_date)->toDateString(),
            'gender' => $representative->gender,
            'address_details' => $representative->address,
            'courier_type' => $this->unmapCourierType($representative->account_type?->value ?? $representative->account_type),
            'warehouse_id' => $representative->warehouse_id,
            'work_types' => $this->unmapWorkTypes($representative->workTypes),
            'governorate_ids' => $representative->governorates->pluck('id')->values(),
            'city_ids' => $representative->cities->pluck('id')->values(),
            'villages_service_enabled' => (bool) $representative->village_service,
            'approval_status' => $this->approvalStatus($representative),
            'transport' => [
                'transport_type_id' => $representative->vehicle?->transport_type_id,
                'plate_number' => trim((string) $representative->vehicle?->registration_number),
                'max_weight_kg' => (float) ($representative->vehicle?->transportType?->max_weight ?? $representative->vehicle?->max_weight ?? 0),
                'max_volume_m3' => (float) ($representative->vehicle?->transportType?->max_volume ?? $representative->vehicle?->max_volume ?? 0),
            ],
            'documents' => $representative->mediaFiles
                ->where('collection_name', 'representative_documents')
                ->map(fn ($file) => [
                    'id' => $file->id,
                    'document_type' => $file->document_type,
                    'url' => $file->url,
                ])
                ->values(),
        ];
    }

    public function formatOrderItem(OrderItem $orderItem): array
    {
        $pickupAddress = $orderItem->pickup_address_text ?: $orderItem->package?->pickupAddress?->full_address;
        $dropoffAddress = $orderItem->dropoff_address_text ?: $orderItem->package?->dropoffAddress?->full_address;

        return [
            'id' => $orderItem->id,
            'order_number' => $orderItem->order?->order_number ?? ('MET-' . $orderItem->id),
            'status' => $orderItem->status,
            'priority' => data_get($orderItem->order, 'metadata.priority', 'normal'),
            'distance_km' => $orderItem->distance_km,
            'pickup_address' => $pickupAddress,
            'dropoff_address' => $dropoffAddress,
            'estimated_fee' => (float) ($orderItem->accepted_fee ?? $orderItem->est_price ?? 0),
            'created_at' => optional($orderItem->created_at)->toIso8601String(),
            'can_start' => $orderItem->representative_id === null && $orderItem->status === 'pending',
        ];
    }

    public function formatOrderDetails(OrderItem $orderItem): array
    {
        return [
            'id' => $orderItem->id,
            'order_number' => $orderItem->order?->order_number ?? ('MET-' . $orderItem->id),
            'priority' => data_get($orderItem->order, 'metadata.priority', 'normal'),
            'distance_km' => $orderItem->distance_km,
            'pickup_address' => $orderItem->pickup_address_text ?: $orderItem->package?->pickupAddress?->full_address,
            'dropoff_address' => $orderItem->dropoff_address_text ?: $orderItem->package?->dropoffAddress?->full_address,
            'sender' => [
                'name' => $orderItem->pickup_contact_name,
                'phone' => $orderItem->pickup_contact_phone,
            ],
            'receiver' => [
                'name' => $orderItem->dropoff_contact_name,
                'phone' => $orderItem->dropoff_contact_phone,
            ],
            'parcels' => [[
                'description' => $orderItem->package?->type?->name ?? 'Parcel',
                'weight' => (float) ($orderItem->package?->weight ?? 0),
                'volume' => (float) data_get($orderItem->package, 'metadata.volume', 0),
            ]],
            'fee' => (float) ($orderItem->accepted_fee ?? $orderItem->est_price ?? 0),
            'status' => $orderItem->status,
        ];
    }

    public function incomingShippingRequestsQuery(Representative $representative): Builder
    {
        $query = ShipmentRequest::query()
            ->with([
                'senderContact.primaryAddress.governorate',
                'senderContact.primaryAddress.city',
                'receiverContact.primaryAddress.governorate',
                'receiverContact.primaryAddress.city',
                'packages',
            ])
            ->whereNull('representative_id')
            ->where('status', ShipmentRequestStatus::SUBMITTED->value);

        $governorateIds = $representative->governorates->pluck('id');

        if ($governorateIds->isNotEmpty()) {
            $query->whereHas('senderContact.primaryAddress', function (Builder $builder) use ($governorateIds) {
                $builder->whereIn('governorate_id', $governorateIds);
            });
        }

        return $query->latest();
    }

    public function activeShippingRequestQuery(Representative $representative): Builder
    {
        return ShipmentRequest::query()
            ->with([
                'senderContact.primaryAddress.governorate',
                'senderContact.primaryAddress.city',
                'receiverContact.primaryAddress.governorate',
                'receiverContact.primaryAddress.city',
                'packages',
            ])
            ->where('representative_id', $representative->id)
            ->where('status', ShipmentRequestStatus::ASSIGNED->value);
    }

    public function formatShippingRequest(ShipmentRequest $request): array
    {
        return [
            'id' => $request->id,
            'request_number' => $request->request_number,
            'sender_name' => $request->senderContact?->full_name,
            'sender_phone' => $request->senderContact?->primary_mobile,
            'receiver_name' => $request->receiverContact?->full_name,
            'receiver_phone' => $request->receiverContact?->primary_mobile,
            'pickup_address' => $request->senderContact?->primaryAddress?->address_line_1,
            'dropoff_address' => $request->receiverContact?->primaryAddress?->address_line_1,
            'package_count' => $request->packages->count(),
            'status' => $request->status?->value ?? $request->status,
            'created_at' => $request->created_at?->toIso8601String(),
        ];
    }

    public function formatShippingRequestDetails(ShipmentRequest $request): array
    {
        return [
            'id' => $request->id,
            'request_number' => $request->request_number,
            'sender' => [
                'name' => $request->senderContact?->full_name,
                'phone' => $request->senderContact?->primary_mobile,
                'address' => $request->senderContact?->primaryAddress?->address_line_1,
            ],
            'receiver' => [
                'name' => $request->receiverContact?->full_name,
                'phone' => $request->receiverContact?->primary_mobile,
                'address' => $request->receiverContact?->primaryAddress?->address_line_1,
            ],
            'packages' => $request->packages->map(fn ($pkg) => [
                'name' => $pkg->package_name,
                'type' => $pkg->package_type,
                'weight' => (float) $pkg->weight,
                'quantity' => $pkg->quantity,
            ])->values(),
            'notes' => $request->notes,
            'status' => $request->status?->value ?? $request->status,
            'created_at' => $request->created_at?->toIso8601String(),
        ];
    }

    public function validateStepFourRules(array $workTypes, array $governorateIds, array $cityIds): void
    {
        if (in_array('delivery_inside_governorate', $workTypes, true) && count($governorateIds) !== 1) {
            throw ValidationException::withMessages([
                'governorate_ids' => ['Local delivery couriers can only select one governorate.'],
            ]);
        }

        if (! in_array('delivery_inside_governorate', $workTypes, true) && ! empty($cityIds)) {
            throw ValidationException::withMessages([
                'city_ids' => ['Cities are only used for delivery inside governorate.'],
            ]);
        }

        if (! empty($cityIds) && ! empty($governorateIds)) {
            $invalidCities = City::query()
                ->whereIn('id', $cityIds)
                ->whereNotIn('governorate_id', $governorateIds)
                ->pluck('id')
                ->all();

            if ($invalidCities !== []) {
                throw ValidationException::withMessages([
                    'city_ids' => ['Selected cities must belong to the selected governorates.'],
                ]);
            }
        }
    }

    public function createResetToken(User $user): string
    {
        $token = Str::random(40);
        $metadata = $user->representative?->metadata ?? [];
        $metadata['password_reset_token'] = $token;
        $metadata['password_reset_expires_at'] = now()->addMinutes((int) setting('otp_expiry_minutes', 5))->toIso8601String();

        if ($user->representative) {
            $user->representative->update(['metadata' => $metadata]);
        }

        return $token;
    }

    public function validateResetToken(User $user, string $token): bool
    {
        $metadata = $user->representative?->metadata ?? [];
        $storedToken = data_get($metadata, 'password_reset_token');
        $expiresAt = data_get($metadata, 'password_reset_expires_at');

        return $storedToken === $token
            && $expiresAt
            && Carbon::parse($expiresAt)->greaterThanOrEqualTo(now());
    }

    public function clearResetToken(User $user): void
    {
        if (! $user->representative) {
            return;
        }

        $metadata = $user->representative->metadata ?? [];
        unset($metadata['password_reset_token'], $metadata['password_reset_expires_at']);
        $user->representative->update(['metadata' => $metadata]);
    }

    public function assertRepresentativeExists(User $user): Representative
    {
        $representative = $user->representative;

        if (! $representative) {
            throw (new ModelNotFoundException())->setModel(Representative::class);
        }

        return $this->getRepresentativeForUser($user);
    }

    public function warehouseList(): Collection
    {
        return Warehouse::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Warehouse $warehouse) => [
                'id' => $warehouse->id,
                'account_number' => $warehouse->warehouse_number,
                'name' => $warehouse->name,
            ])
            ->values();
    }

    public function transportTypeList(): Collection
    {
        return TransportType::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(fn (TransportType $transportType) => [
                'id' => $transportType->id,
                'code' => $transportType->code,
                'name' => $transportType->name_ar ?: $transportType->name_en,
                'max_weight_kg' => (float) $transportType->max_weight,
                'max_volume_m3' => (float) $transportType->max_volume,
                'requires_driving_license' => (bool) $transportType->requires_driving_license,
                'requires_vehicle_license' => (bool) $transportType->requires_vehicle_license,
            ])
            ->values();
    }

    public function storeVehicleImage(Representative $representative, string $path): void
    {
        $metadata = $representative->metadata ?? [];
        $metadata['vehicle_image'] = $path;
        $representative->update(['metadata' => $metadata]);
    }

    public function unreadNotificationsCount(User $user): int
    {
        return method_exists($user, 'unreadNotifications')
            ? $user->unreadNotifications()->count()
            : DB::table('notifications')
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', $user->id)
                ->whereNull('read_at')
                ->count();
    }
}
