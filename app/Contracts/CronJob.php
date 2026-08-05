<?php

namespace App\Contracts;

use App\Services\Cron\CronJobResult;

/**
 * A single scheduled cron job that can be run by the scheduler and
 * manually from the admin monitor page.
 */
interface CronJob
{
    /**
     * Stable job key used for scheduling, logging and the monitor page URL.
     */
    public function name(): string;

    /**
     * Run the job synchronously and report how many records were scanned
     * and how many were actually changed.
     */
    public function handle(): CronJobResult;
}
