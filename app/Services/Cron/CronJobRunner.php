<?php

namespace App\Services\Cron;

use App\Contracts\CronJob;
use App\Models\CronJobLog;
use Illuminate\Support\Facades\Log;

/**
 * Wraps every cron job execution (scheduled or manual) with a single
 * `cron_job_logs` row, so the admin monitor page can track status, duration,
 * records processed/affected and any error message.
 */
class CronJobRunner
{
    public function __construct(
        protected CronJobRegistry $registry
    ) {}

    public function run(CronJob $job): CronJobLog
    {
        $startedAt = now();

        $log = CronJobLog::create([
            'job_name' => $job->name(),
            'status' => CronJobLog::STATUS_RUNNING,
            'started_at' => $startedAt,
        ]);

        try {
            $result = $job->handle();

            $log->update([
                'status' => CronJobLog::STATUS_SUCCESS,
                'finished_at' => now(),
                'duration' => $this->durationInSeconds($startedAt),
                'records_processed' => $result->processed,
                'records_affected' => $result->affected,
                'metadata' => $result->metadata,
            ]);

            Log::info("Cron job [{$job->name()}] succeeded", [
                'processed' => $result->processed,
                'affected' => $result->affected,
            ]);
        } catch (\Throwable $throwable) {
            $log->update([
                'status' => CronJobLog::STATUS_FAILED,
                'finished_at' => now(),
                'duration' => $this->durationInSeconds($startedAt),
                'error_message' => $throwable->getMessage(),
            ]);

            Log::error("Cron job [{$job->name()}] failed", [
                'exception' => $throwable,
            ]);

            report($throwable);
        }

        return $log->fresh();
    }

    public function runByKey(string $jobKey): CronJobLog
    {
        return $this->run($this->registry->resolve($jobKey));
    }

    protected function durationInSeconds(\Illuminate\Support\Carbon $startedAt): float
    {
        return round((float) $startedAt->diffInSeconds(now()), 3);
    }
}
