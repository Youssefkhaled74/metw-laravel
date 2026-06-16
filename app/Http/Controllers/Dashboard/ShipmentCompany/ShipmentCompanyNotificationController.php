<?php

namespace App\Http\Controllers\Dashboard\ShipmentCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShipmentCompanyNotificationController extends Controller
{

    public function json()
    {
        $admin = auth('shipment')->user();
        return response()->json($admin->unreadNotifications);
    }
    public function index(Request $request)
    {
        $shipment = auth('shipment')->user();

        $notifications = $shipment->notifications()->latest()->paginate(20);

        return view('dashboard.shipment.notifications', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $shipment = auth('shipment')->user();
        $notification = $shipment->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $shipment = auth('shipment')->user();
        $shipment->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
