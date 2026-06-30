<?php

namespace App\Services;

use App\Enum\ShipmentContactType;
use App\Enum\ShipmentRequestStatus;
use App\Models\ShipmentContact;
use App\Models\ShipmentRequest;
use App\Models\ShipmentRequestPackage;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShipmentRequestService
{
    public function listForUser(User $user)
    {
        return ShipmentRequest::query()
            ->where('user_id', $user->id)
            ->with($this->relations())
            ->withCount('packages')
            ->latest()
            ->get();
    }

    public function getForUserOrFail(User $user, int $id): ShipmentRequest
    {
        return ShipmentRequest::query()
            ->where('user_id', $user->id)
            ->with($this->relations())
            ->withCount('packages')
            ->findOrFail($id);
    }

    public function create(User $user, array $data): ShipmentRequest
    {
        return DB::transaction(function () use ($user, $data) {
            $this->assertOwnedContact($user, $data['sender_contact_id'], ShipmentContactType::SENDER);
            $this->assertOwnedContact($user, $data['receiver_contact_id'], ShipmentContactType::RECEIVER);

            $request = ShipmentRequest::create([
                'user_id' => $user->id,
                'sender_contact_id' => $data['sender_contact_id'],
                'receiver_contact_id' => $data['receiver_contact_id'],
                'status' => ShipmentRequestStatus::DRAFT->value,
                'notes' => $data['notes'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            return $request->fresh($this->relations())->loadCount('packages');
        });
    }

    public function addPackage(User $user, int $requestId, array $data, Request $httpRequest): ShipmentRequest
    {
        $shipmentRequest = $this->getDraftForUserOrFail($user, $requestId);

        return DB::transaction(function () use ($shipmentRequest, $data, $httpRequest) {
            $package = $shipmentRequest->packages()->create([
                'package_name' => $data['package_name'],
                'package_type' => $data['package_type'] ?? null,
                'quantity' => $data['quantity'] ?? 1,
                'weight' => $data['weight'] ?? null,
                'length' => $data['length'] ?? null,
                'width' => $data['width'] ?? null,
                'height' => $data['height'] ?? null,
                'declared_value' => $data['declared_value'] ?? null,
                'notes' => $data['notes'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            $this->storePackageImages($package, $httpRequest);

            return $shipmentRequest->fresh($this->relations())->loadCount('packages');
        });
    }

    public function removePackage(User $user, int $requestId, int $packageId): ShipmentRequest
    {
        $shipmentRequest = $this->getDraftForUserOrFail($user, $requestId);

        return DB::transaction(function () use ($shipmentRequest, $packageId) {
            $package = $shipmentRequest->packages()->find($packageId);

            if (! $package) {
                throw (new ModelNotFoundException())->setModel(ShipmentRequestPackage::class, [$packageId]);
            }

            $package->mediaFiles()->delete();
            $package->delete();

            return $shipmentRequest->fresh($this->relations())->loadCount('packages');
        });
    }

    public function submit(User $user, int $requestId): ShipmentRequest
    {
        $shipmentRequest = $this->getDraftForUserOrFail($user, $requestId);

        if (! $shipmentRequest->packages()->exists()) {
            throw ValidationException::withMessages([
                'packages' => ['At least one package is required before submitting the shipment request.'],
            ]);
        }

        $shipmentRequest->update([
            'status' => ShipmentRequestStatus::SUBMITTED->value,
            'submitted_at' => now(),
        ]);

        return $shipmentRequest->fresh($this->relations())->loadCount('packages');
    }

    protected function getDraftForUserOrFail(User $user, int $requestId): ShipmentRequest
    {
        $shipmentRequest = ShipmentRequest::query()
            ->where('user_id', $user->id)
            ->findOrFail($requestId);

        if (($shipmentRequest->status?->value ?? $shipmentRequest->status) !== ShipmentRequestStatus::DRAFT->value) {
            throw ValidationException::withMessages([
                'shipment_request' => ['Only draft shipment requests can be modified.'],
            ]);
        }

        return $shipmentRequest;
    }

    protected function assertOwnedContact(User $user, int $contactId, ShipmentContactType $type): ShipmentContact
    {
        $contact = ShipmentContact::query()
            ->where('user_id', $user->id)
            ->where('type', $type->value)
            ->find($contactId);

        if (! $contact) {
            throw ValidationException::withMessages([
                $type === ShipmentContactType::SENDER ? 'sender_contact_id' : 'receiver_contact_id' =>
                    ['The selected ' . $type->value . ' contact is invalid.'],
            ]);
        }

        return $contact;
    }

    protected function storePackageImages(ShipmentRequestPackage $package, Request $httpRequest): void
    {
        $files = $httpRequest->file('images', []);

        if (empty($files)) {
            return;
        }

        $directory = 'storage/shipment-requests/packages/' . $package->id;
        File::ensureDirectoryExists(public_path($directory));

        foreach ($files as $index => $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = now()->format('YmdHis') . '_' . Str::uuid() . '.' . $extension;
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $size = $file->getSize();

            $file->move(public_path($directory), $filename);

            $relativePath = $directory . '/' . $filename;

            $package->mediaFiles()->create([
                'collection_name' => 'shipment_request_package_images',
                'disk' => 'public',
                'directory' => $directory,
                'filename' => $filename,
                'original_name' => $originalName,
                'extension' => $extension,
                'mime_type' => $mimeType,
                'size' => $size,
                'url' => asset($relativePath),
                'title' => pathinfo($originalName, PATHINFO_FILENAME),
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }
    }

    protected function relations(): array
    {
        return [
            'senderContact.primaryAddress.country',
            'senderContact.primaryAddress.state',
            'senderContact.primaryAddress.governorate',
            'senderContact.primaryAddress.city',
            'senderContact.primaryAddress.zone',
            'receiverContact.primaryAddress.country',
            'receiverContact.primaryAddress.state',
            'receiverContact.primaryAddress.governorate',
            'receiverContact.primaryAddress.city',
            'receiverContact.primaryAddress.zone',
            'packages.mediaFiles',
        ];
    }
}
