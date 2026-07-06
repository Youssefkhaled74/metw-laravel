<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Order;

use App\Enum\ComplaintStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use App\Models\EcommerceOrder;
use App\Models\Representative;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return responseJson(true, 'Complaints retrieved successfully', $complaints);
    }

    public function store(StoreComplaintRequest $request)
    {
        $complaintableType = $this->resolveComplaintableType($request->input('complaintable_type'));
        if (!$complaintableType) {
            return responseJson(false, 'Complaint target is invalid', null, 422);
        }

        $complaintable = $complaintableType::find($request->input('complaintable_id'));
        if (!$complaintable) {
            return responseJson(false, 'Complaint target not found', null, 404);
        }

        if (in_array($complaintableType, [ReturnRequest::class, EcommerceOrder::class], true) && (int) $complaintable->user_id !== (int) Auth::id()) {
            return responseJson(false, 'Complaint target not found', null, 404);
        }

        $complaint = Complaint::create([
            'user_id' => Auth::id(),
            'complaint_type' => $request->input('complaint_type'),
            'complaintable_type' => $complaintableType,
            'complaintable_id' => $complaintable->id,
            'subject' => $request->input('subject'),
            'description' => $request->input('description'),
            'reason' => $request->input('reason'),
            'status' => ComplaintStatus::PENDING->value,
        ]);

        return responseJson(true, 'Complaint submitted successfully', $complaint, 201);
    }

    public function show($id)
    {
        $complaint = Complaint::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return responseJson(true, 'Complaint retrieved successfully', $complaint);
    }

    private function resolveComplaintableType(?string $type): ?string
    {
        $aliases = [
            'user' => User::class,
            'vendor' => Vendor::class,
            'warehouse' => Warehouse::class,
            'representative' => Representative::class,
            'return_request' => ReturnRequest::class,
            'order' => EcommerceOrder::class,
        ];

        if (!$type) {
            return null;
        }

        if (isset($aliases[$type])) {
            return $aliases[$type];
        }

        return class_exists($type) ? $type : null;
    }
}
