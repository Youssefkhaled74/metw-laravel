<?php

namespace App\Http\Requests\Dashboard\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingTimersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'working_hours_start' => ['required', 'date_format:H:i'],
            'working_hours_end' => ['required', 'date_format:H:i', 'after:working_hours_start'],
            'auto_reject_working_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'response_window_working_hours' => ['required', 'integer', 'min:1', 'max:72'],
            'cancel_unpaid_advance_hours' => ['required', 'integer', 'min:1', 'max:2160'],
            'aggregate_sub_shipments_days' => ['required', 'integer', 'min:1', 'max:90'],
            'aggregate_sub_shipments_scope' => ['required', 'in:all,warehouse,group'],
            'aggregate_sub_shipments_target_id' => [
                'nullable',
                'integer',
                'min:1',
                'required_if:aggregate_sub_shipments_scope,warehouse',
                'required_if:aggregate_sub_shipments_scope,group',
            ],
            'execution_start_hours' => ['required', 'integer', 'min:1', 'max:2160'],
        ];
    }

    public function messages(): array
    {
        return [
            'working_hours_end.after' => 'Working hours end must be after the start time.',
            'aggregate_sub_shipments_target_id.required_if' => 'Please choose a warehouse or governorate for the aggregation scope.',
        ];
    }
}
