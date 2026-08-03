<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierFailureMessageController extends Controller
{
    protected const KEYS = [
        'courier_failure_message_shipping',
        'courier_failure_message_delivery',
    ];

    public function index()
    {
        if (Auth::guard('employee')->check()
            && ! Auth::guard('employee')->user()->can('admin.settings.courier-failure-messages.index')) {
            return view('dashboard.admin.no-permission');
        }

        $values = [];
        foreach (self::KEYS as $key) {
            $values[$key] = Setting::where('key', $key)->value('value');
        }

        return view('dashboard.admin.settings.courier-failure-messages.index', compact('values'));
    }

    public function update(Request $request)
    {
        if (Auth::guard('employee')->check()
            && ! Auth::guard('employee')->user()->can('admin.settings.courier-failure-messages.update')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'courier_failure_message_shipping' => ['nullable', 'string', 'max:5000'],
            'courier_failure_message_delivery' => ['nullable', 'string', 'max:5000'],
        ]);

        foreach (self::KEYS as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $data[$key] ?? '']
            );
        }

        return redirect()->route('admin.settings.courier-failure-messages.index')
            ->with('success', 'Courier failure messages updated successfully.');
    }
}
