<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorNotificationController extends Controller
{

    public function json()
    {
        $admin = auth('vendor')->user();
        return response()->json($admin->unreadNotifications);
    }
    public function index(Request $request)
    {
        $shipment = auth('vendor')->user();

        $notifications = $shipment->notifications()->latest()->paginate(20);

        return view('dashboard.vendor.notifications', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $shipment = auth('vendor')->user();
        $notification = $shipment->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $shipment = auth('vendor')->user();
        $shipment->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
