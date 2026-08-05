<?php

namespace App\Console\Commands;

use App\Models\CronJobLog;
use App\Services\Cron\CronJobRunner;
use Illuminate\Console\Command;

class RunScheduledJob extends Command
{
    protected $signature = 'cron:run {job : Cron job key registered in CronJobRegistry}';

    protected $description = 'Run a registered cron job manually; execution is logged in cron_job_logs.';

    public function handle(CronJobRunner $runner): int
    {
        $key = $this->argument('job');

        try {
            $log = $runner->runByKey($key);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($log->status === CronJobLog::STATUS_FAILED) {
            $this->error("Cron job '{$key}' failed: {$log->error_message}");

            return self::FAILURE;
        }

        $this->info("Cron job '{$key}' succeeded.");
        $this->line("Processed: {$log->records_processed} / Affected: {$log->records_affected}");

        return self::SUCCESS;
    }
}
