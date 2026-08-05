<?php

namespace App\Services\Cron;

/**
 * Result of a single cron job execution.
 */
class CronJobResult
{
    public function __construct(
        public int $processed,
        public int $affected,
        public array $metadata = []
    ) {}
}
