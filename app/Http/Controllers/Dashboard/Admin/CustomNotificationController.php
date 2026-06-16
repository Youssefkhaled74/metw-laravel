<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShipmentCompany;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\CustomAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class CustomNotificationController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'username', 'email')->get();
        $vendors = Vendor::withoutGlobalScope('active')->select('id', 'name', 'email')->get();
        $shipmentCompanies = ShipmentCompany::withoutGlobalScope('active')->select('id', 'name', 'email')->get();

        return view('dashboard.admin.custom-notifications.index', compact('users', 'vendors', 'shipmentCompanies'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'body'    => 'required|string',
            'target'  => 'required|in:all,one,multiple',
            'recipient_type' => 'required|in:user,vendor,shipment_company',
            'app_type'=> 'required|in:ecommerce,shipment,both',
            'user_id' => 'nullable|required_if:target,one|integer',
            'users'   => 'nullable|required_if:target,multiple|array',
            'users.*' => 'integer',
        ]);

        $request->validate([
            'user_id' => 'nullable|required_if:target,one|exists:' . $this->resolveRecipientsTable($request->recipient_type) . ',id',
            'users.*' => 'nullable|exists:' . $this->resolveRecipientsTable($request->recipient_type) . ',id',
        ]);


        $notification = new CustomAdminNotification(
            title: $request->title,
            body: $request->body,
            data: [],
            type: 'custom',
            navigationType: 'none',
            appType: $request->app_type
        );

        $recipientQuery = $this->resolveRecipientQuery($request->recipient_type);

        match ($request->target) {
            'all' => Notification::send((clone $recipientQuery)->get(), $notification),
            'one' => (clone $recipientQuery)->find($request->user_id)?->notify($notification),
            'multiple' => Notification::send((clone $recipientQuery)->whereIn('id', $request->users ?? [])->get(), $notification),
        };

        return back()->with('success', __('admin-dashboard.notification_sent'));
    }

    private function resolveRecipientsTable(string $recipientType): string
    {
        return match ($recipientType) {
            'vendor' => 'vendors',
            'shipment_company' => 'shipment_companies',
            default => 'users',
        };
    }

    private function resolveRecipientQuery(string $recipientType)
    {
        return match ($recipientType) {
            'vendor' => Vendor::withoutGlobalScope('active')->select('id', 'name', 'email'),
            'shipment_company' => ShipmentCompany::withoutGlobalScope('active')->select('id', 'name', 'email'),
            default => User::select('id', 'username', 'email'),
        };
    }
}
