<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\ComplaintStatus;
use App\Enum\ComplaintType;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'complaint_type' => ['nullable', 'string', Rule::in(array_column(ComplaintType::cases(), 'value'))],
            'status' => ['nullable', 'string', Rule::in(array_column(ComplaintStatus::cases(), 'value'))],
        ]);

        $complaints = Complaint::query()
            ->with(['user', 'complaintable'])
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('complaint_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('username', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($validated['complaint_type'] ?? null, fn ($query, $type) => $query->where('complaint_type', $type))
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.admin.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['user', 'complaintable']);

        $related = $this->resolveRelatedLink($complaint);

        return view('dashboard.admin.complaints.show', compact('complaint', 'related'));
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_column(ComplaintStatus::cases(), 'value'))],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $complaint->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $complaint->admin_notes,
            'resolved_at' => in_array($validated['status'], [ComplaintStatus::RESOLVED->value, ComplaintStatus::REJECTED->value], true)
                ? now()
                : null,
        ]);

        return redirect()->back()->with('success', 'تم تحديث الشكوى بنجاح.');
    }

    private function resolveRelatedLink(Complaint $complaint): array
    {
        $model = $complaint->complaintable;

        if (!$model) {
            return ['label' => null, 'url' => null];
        }

        $type = class_basename($complaint->complaintable_type);

        return match ($type) {
            'ReturnRequest' => [
                'label' => $model->return_number ?? ('#' . $model->id),
                'url' => route('admin.return-requests.show', $model->id),
            ],
            'EcommerceOrder' => [
                'label' => $model->order_number ?? ('#' . $model->id),
                'url' => route('admin.ecommerce-orders.show', $model->id),
            ],
            'User' => [
                'label' => $model->username ?? $model->email ?? ('#' . $model->id),
                'url' => route('admin.users.show', $model->id),
            ],
            'Vendor' => [
                'label' => $model->name ?? ('#' . $model->id),
                'url' => route('admin.vendors.show', $model->id),
            ],
            'Representative' => [
                'label' => $model->account_number ?? ('#' . $model->id),
                'url' => route('admin.representatives.show', $model->id),
            ],
            default => [
                'label' => $model->name ?? $model->title ?? ('#' . $model->id),
                'url' => null,
            ],
        };
    }
}
