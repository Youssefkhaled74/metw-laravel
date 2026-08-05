<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Admin\Settings\UpdateShippingTimersRequest;
use App\Models\Governorate;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Services\CourierSystem\CourierSystemConfigService;
use Illuminate\Support\Facades\Auth;

class ShippingTimerSettingController extends Controller
{
    public const KEYS = [
        CourierSystemConfigService::WORKING_HOURS_START,
        CourierSystemConfigService::WORKING_HOURS_END,
        CourierSystemConfigService::AUTO_REJECT_WORKING_HOURS,
        CourierSystemConfigService::RESPONSE_WINDOW_WORKING_HOURS,
        CourierSystemConfigService::CANCEL_UNPAID_ADVANCE_HOURS,
        CourierSystemConfigService::AGGREGATE_SUB_SHIPMENTS_DAYS,
        CourierSystemConfigService::AGGREGATE_SUB_SHIPMENTS_SCOPE,
        CourierSystemConfigService::AGGREGATE_SUB_SHIPMENTS_TARGET_ID,
        CourierSystemConfigService::EXECUTION_START_HOURS,
    ];

    public function __construct(
        protected CourierSystemConfigService $config
    ) {
        $this->middleware('admin');
    }

    public function index()
    {
        if (Auth::guard('employee')->check()
            && ! Auth::guard('employee')->user()->can('admin.settings.shipping-timers.index')) {
            return view('dashboard.admin.no-permission');
        }

        $values = [];
        foreach (self::KEYS as $key) {
            $values[$key] = $this->config->get($key);
        }

        $lastUpdated = Setting::whereIn('key', self::KEYS)
            ->whereNotNull('updated_by')
            ->latest('updated_at')
            ->first();

        $lastUpdatedName = null;
        if ($lastUpdated) {
            $admin = \App\Models\Admin::find($lastUpdated->updated_by);
            $employee = $admin ? null : \App\Models\Employee::find($lastUpdated->updated_by);

            $lastUpdatedName = $admin?->name
                ?? ($employee ? trim($employee->first_name . ' ' . $employee->last_name) : null)
                ?? ('#' . $lastUpdated->updated_by);
        }

        return view('dashboard.admin.settings.shipping-timers.index', [
            'values' => $values,
            'lastUpdated' => $lastUpdated,
            'lastUpdatedName' => $lastUpdatedName,
            'warehouses' => Warehouse::query()->orderBy('name')->get(['id', 'name']),
            'governorates' => Governorate::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateShippingTimersRequest $request)
    {
        if (Auth::guard('employee')->check()
            && ! Auth::guard('employee')->user()->can('admin.settings.shipping-timers.update')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validated();

        $this->config->update($data);
        $this->markUpdatedBy(array_keys($data));

        return redirect()->route('admin.settings.shipping-timers.index')
            ->with('success', 'Shipping timers updated successfully.');
    }

    public function reset()
    {
        if (Auth::guard('employee')->check()
            && ! Auth::guard('employee')->user()->can('admin.settings.shipping-timers.update')) {
            return view('dashboard.admin.no-permission');
        }

        $defaults = [
            CourierSystemConfigService::WORKING_HOURS_START => '10:00',
            CourierSystemConfigService::WORKING_HOURS_END => '22:00',
            CourierSystemConfigService::AUTO_REJECT_WORKING_HOURS => '7',
            CourierSystemConfigService::RESPONSE_WINDOW_WORKING_HOURS => '3',
            CourierSystemConfigService::CANCEL_UNPAID_ADVANCE_HOURS => '12',
            CourierSystemConfigService::AGGREGATE_SUB_SHIPMENTS_DAYS => '3',
            CourierSystemConfigService::AGGREGATE_SUB_SHIPMENTS_SCOPE => 'all',
            CourierSystemConfigService::AGGREGATE_SUB_SHIPMENTS_TARGET_ID => '',
            CourierSystemConfigService::EXECUTION_START_HOURS => '24',
        ];

        $this->config->update($defaults);
        $this->markUpdatedBy(array_keys($defaults));

        return redirect()->route('admin.settings.shipping-timers.index')
            ->with('success', 'Shipping timers reset to default values.');
    }

    protected function markUpdatedBy(array $keys): void
    {
        if (empty($keys)) {
            return;
        }

        Setting::whereIn('key', $keys)->update(['updated_by' => $this->currentAdminId()]);
    }

    protected function currentAdminId(): ?int
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->id();
        }

        if (Auth::guard('employee')->check()) {
            return Auth::guard('employee')->id();
        }

        return null;
    }
}
