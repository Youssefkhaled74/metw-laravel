<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function json()
    {
        // if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.notifications')) {
        //     return view('dashboard.admin.no-permission');
        // }
        $admin = auth('admin')->user();
        return response()->json($admin->unreadNotifications);
    }
    public function index(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.notifications.index')) {
            return view('dashboard.admin.no-permission');
        }
        $admin = auth('admin')->user();

        $notifications = $admin->notifications()->latest()->paginate(20);

        return view('dashboard.admin.notifications', compact('notifications'));
    }

    public function markAsRead($id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.notifications.read')) {
            return view('dashboard.admin.no-permission');
        }
        $admin = auth('admin')->user();
        $notification = $admin->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.notifications.readAll')) {
            return view('dashboard.admin.no-permission');
        }
        $admin = auth('admin')->user();
        $admin->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
