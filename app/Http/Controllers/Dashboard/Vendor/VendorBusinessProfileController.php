<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\CompleteVendorBusinessProfileRequest;
use App\Services\CompleteVendorBusinessProfileService;

class VendorBusinessProfileController extends Controller
{
    public function __construct(
        protected CompleteVendorBusinessProfileService $completeVendorBusinessProfileService
    ) {
    }

    public function upsert(CompleteVendorBusinessProfileRequest $request)
    {
        $this->completeVendorBusinessProfileService->complete(
            auth('vendor')->user(),
            $request->validated(),
            $request
        );

        return redirect()
            ->back()
            ->with('success', 'Business profile submitted for review successfully.');
    }
}
