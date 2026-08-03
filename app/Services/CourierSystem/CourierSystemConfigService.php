<?php

namespace App\Services\CourierSystem;

use App\Models\Setting;

class CourierSystemConfigService
{
    public const WORKING_HOURS_START = 'working_hours_start';
    public const WORKING_HOURS_END = 'working_hours_end';
    public const AUTO_REJECT_WORKING_HOURS = 'auto_reject_working_hours';
    public const RESPONSE_WINDOW_WORKING_HOURS = 'response_window_working_hours';
    public const TIMEOUT_AUTO_ACTION = 'courier_timeout_auto_action';
    public const MAX_OFFERS_PER_LEG = 'courier_max_offers_per_leg';

    /** Failure notification text when no shipping courier accepts (Sections 3 & 4). */
    public const FAILURE_SHIPPING_MESSAGE = 'courier_failure_message_shipping';

    /** Failure notification text when no delivery courier accepts (Section 4). */
    public const FAILURE_DELIVERY_MESSAGE = 'courier_failure_message_delivery';

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return [
            self::WORKING_HOURS_START => $this->get(self::WORKING_HOURS_START, '10:00'),
            self::WORKING_HOURS_END => $this->get(self::WORKING_HOURS_END, '22:00'),
            self::AUTO_REJECT_WORKING_HOURS => $this->get(self::AUTO_REJECT_WORKING_HOURS, '7'),
            self::RESPONSE_WINDOW_WORKING_HOURS => $this->get(self::RESPONSE_WINDOW_WORKING_HOURS, '3'),
            self::TIMEOUT_AUTO_ACTION => $this->get(self::TIMEOUT_AUTO_ACTION, 'auto_assign_next'),
            self::MAX_OFFERS_PER_LEG => $this->get(self::MAX_OFFERS_PER_LEG, '5'),
        ];
    }

    public function get(string $key, $default = null)
    {
        return setting($key, $default);
    }

    public function workingHoursStart(): string
    {
        return (string) $this->get(self::WORKING_HOURS_START, '10:00');
    }

    public function workingHoursEnd(): string
    {
        return (string) $this->get(self::WORKING_HOURS_END, '22:00');
    }

    public function autoRejectWorkingHours(): int
    {
        return max(1, (int) $this->get(self::AUTO_REJECT_WORKING_HOURS, 7));
    }

    public function responseWindowWorkingHours(): int
    {
        return max(1, (int) $this->get(self::RESPONSE_WINDOW_WORKING_HOURS, 3));
    }

    public function timeoutAutoAction(): string
    {
        return (string) $this->get(self::TIMEOUT_AUTO_ACTION, 'auto_assign_next');
    }

    public function maxOffersPerLeg(): int
    {
        return max(1, (int) $this->get(self::MAX_OFFERS_PER_LEG, 5));
    }

    public function failureShippingMessage(): string
    {
        return (string) $this->get(
            self::FAILURE_SHIPPING_MESSAGE,
            'Shipping service is currently unavailable according to the required shipping path'
        );
    }

    public function failureDeliveryMessage(): string
    {
        return (string) $this->get(
            self::FAILURE_DELIVERY_MESSAGE,
            'Delivery service is currently unavailable according to the required delivery path'
        );
    }

    /**
     * Persist multiple settings at once (admin-configurable values).
     *
     * @param  array<string, mixed>  $values
     */
    public function update(array $values): void
    {
        foreach ($values as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
        }
    }
}
