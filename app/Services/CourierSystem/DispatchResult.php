<?php

namespace App\Services\CourierSystem;

use App\Enum\CourierRequestType;
use App\Models\RequestPath;
use Illuminate\Support\Collection;

class DispatchResult
{
    /**
     * @param  Collection<int, RequestPath>  $paths
     * @param  Collection<int, \App\Models\CourierAssignment>  $assignments
     */
    public function __construct(
        public readonly CourierRequestProfile $profile,
        public readonly CourierRequestType $type,
        public readonly Collection $paths,
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
