<?php

namespace App\Services\CourierSystem;

use Carbon\Carbon;
use Carbon\CarbonInterval;

class WorkingHoursService
{
    public function __construct(
        protected CourierSystemConfigService $config
    ) {}

    public function isWithinWorkingHours(?Carbon $date = null): bool
    {
        $date = $date ?: now();

        [$start, $end] = $this->windowForDate($date);

        return $date->betweenIncluded($start, $end);
    }

    /**
     * Count working hours elapsed between two datetimes (Section 3).
     * Only hours inside the configured working window (e.g. 10:00–22:00) count.
     */
    public function workingHoursBetween(Carbon $from, Carbon $to): float
    {
        $from = $from->copy();
        $to = $to->copy();

        if ($to->lessThanOrEqualTo($from)) {
            return 0.0;
        }

        $hours = 0.0;
        $cursor = $from->copy();

        while ($cursor->lessThan($to)) {
            [$start, $end] = $this->windowForDate($cursor);

            $dayStart = max($cursor, $start);
            $dayEnd = min($to, $end);

            if ($dayEnd->greaterThan($dayStart)) {
                $hours += $dayEnd->diffInMinutes($dayStart) / 60;
            }

            $cursor = $cursor->copy()->startOfDay()->addDay();
        }

        return round($hours, 2);
    }

    /**
     * Add a number of *working* hours to a datetime and return the resulting
     * timestamp (used to compute response deadlines that respect working hours).
     */
    public function addWorkingHours(Carbon $from, float $workingHours): Carbon
    {
        if ($workingHours <= 0) {
            return $from->copy();
        }

        $remainingMinutes = $workingHours * 60;
        $cursor = $from->copy();

        while ($remainingMinutes > 0) {
            [$start, $end] = $this->windowForDate($cursor);

            if ($cursor->lessThan($start)) {
                $cursor = $start->copy();
            }

            $availableUntil = $cursor->copy()->addMinutes($remainingMinutes);

            if ($availableUntil->lessThanOrEqualTo($end)) {
                return $availableUntil;
            }

            $remainingMinutes -= $cursor->diffInMinutes($end);
            $cursor = $end->copy()->startOfDay()->addDay()->addSeconds(1);
        }

        return $cursor;
    }

    /**
     * Human readable label for a number of working hours.
     */
    public function label(float $workingHours): string
    {
        return CarbonInterval::minutes((int) round($workingHours * 60))
            ->cascade()
            ->forHumans();
    }

    /**
     * The working window for a given date.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function windowForDate(Carbon $date): array
    {
        [$startHour, $startMinute] = $this->parseTime($this->config->workingHoursStart(), [10, 0]);
        [$endHour, $endMinute] = $this->parseTime($this->config->workingHoursEnd(), [22, 0]);

        $start = Carbon::parse($date->toDateString())->setTime($startHour, $startMinute, 0);
        $end = Carbon::parse($date->toDateString())->setTime($endHour, $endMinute, 0);

        return [$start, $end];
    }

    /**
     * @return array{0: int, 1: int}
     */
    protected function parseTime(string $time, array $default): array
    {
        $parts = explode(':', $time);

        return [
            (int) ($parts[0] ?? $default[0]),
            (int) ($parts[1] ?? $default[1]),
        ];
    }
}
