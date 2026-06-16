<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\State;
use App\Models\City;
use App\Services\GoogleMapsService;
class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Allow either shipment company or suggestion index
            'preferred_company_id' => ['nullable', 'exists:shipment_companies,id'],
            'selected_suggestion_index' => ['nullable', 'integer', 'min:0'],

            // Packages
            'packages' => ['required', 'array', 'min:1'],
            'packages.*.category_id' => ['required', 'integer', 'exists:main_categories,id'],
            'packages.*.sub_category_id' => ['required', 'integer', 'exists:categories,id'],
            'packages.*.weight' => ['required', 'numeric', 'min:0.1'],
            'packages.*.type_id' => ['required', 'integer', 'exists:package_types,id'],
            'packages.*.size' => ['required', 'numeric', 'min:0.1'], // size in cm or whatever unit
            'packages.*.piece' => ['required', 'integer', 'min:1'],
            'packages.*.delivery_type_id' => ['required', 'integer', 'exists:delivery_types,id'],
            'packages.*.consignment_type_id' => ['required', 'integer', 'exists:consignment_types,id'],
            'packages.*.images' => ['nullable', 'array'],
            'packages.*.images.*' => ['nullable', 'image', 'max:2048'],

            // Pickup & Dropoff
            'pickup_address' => ['required', 'array'],
            'pickup_address.address' => ['required', 'string'],
            'pickup_address.phone' => ['required', 'string'],
            'pickup_address.city_id' => ['required', 'integer', 'exists:cities,id'],
            'pickup_address.state_id' => ['required', 'integer', 'exists:states,id'],
            'pickup_address.zone_id' => ['required', 'integer', 'exists:zones,id'],
            'pickup_address.latitude' => ['required', 'string'],
            'pickup_address.longitude' => ['required', 'string'],
            'pickup_address.is_saved' => ['nullable', 'boolean'],
            'pickup_address.is_village' => ['required', 'boolean'],

            'dropoff_address' => ['required', 'array'],
            'dropoff_address.address' => ['required', 'string'],
            'dropoff_address.phone' => ['required', 'string'],
            'dropoff_address.city_id' => ['required', 'integer', 'exists:cities,id'],
            'dropoff_address.state_id' => ['required', 'integer', 'exists:states,id'],
            'dropoff_address.zone_id' => ['required', 'integer', 'exists:zones,id'],
            'dropoff_address.latitude' => ['required', 'string'],
            'dropoff_address.longitude' => ['required', 'string'],
            'dropoff_address.is_saved' => ['nullable', 'boolean'],
            'dropoff_address.is_village' => ['required', 'boolean'],

            // Details
            'details' => ['required', 'array'],
            'details.sender_name' => ['required', 'string'],
            'details.sender_phone' => ['required', 'string'],
            'details.recive_name' => ['required', 'string'],
            'details.recive_phone' => ['required', 'string'],
            'details.pickup_date' => ['nullable', 'date'],
            'details.pickup_time' => ['nullable', 'string'],

            'est_date' => ['nullable', 'date'],
            'est_days' => ['nullable', 'integer', 'min:0'],
            'est_price' => ['nullable', 'numeric', 'min:0'],

            // Split info (optional)
            'split' => ['nullable', 'array'],
            'split.accept' => ['nullable', 'boolean'],
            'split.selected_suggestion_index' => ['nullable', 'integer', 'min:0'],
        ];
    }

    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         $maps = app(GoogleMapsService::class);

    //         foreach (['pickup_address', 'dropoff_address'] as $type) {
    //             if (!$this->has($type)) continue;

    //             $data = $this->input($type);

    //             $state = State::find($data['state_id']);
    //             $city  = City::find($data['city_id']);


    //             if (!$state) {
    //                 $validator->errors()->add($type, 'Invalid state.');
    //                 continue;
    //             }

    //             $isValid = $maps->validateLatLngMatchesState(
    //                 (float) $data['latitude'],
    //                 (float) $data['longitude'],
    //                 $state,
    //                 $city
    //             );

    //             if (!$isValid) {
    //                 $validator->errors()->add(
    //                     $type,
    //                     ucfirst(str_replace('_', ' ', $type)) .
    //                     ' location does not match the selected state/city.'
    //                 );
    //             }
    //         }
    //     });
    // }
}
