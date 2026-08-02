<?php

namespace App\Services\CourierSystem;

use App\Enum\RequestLegType;
use Illuminate\Support\Collection;

class DispatchResult
{
    /**
     * @param  array<int, RequestLegType>  $legs
     * @param  Collection<int, \App\Models\CourierAssignment>  $assignments
     */
    public function __construct(
        public readonly CourierRequestProfile $profile,
        public readonly array $legs,
        public readonly Collection $assignments,
    ) {}

    public function isEmpty(): bool
    {
        return $this->assignments->isEmpty();
    }

    public function count(): int
    {
        return $this->assignments->count();
    }
}
