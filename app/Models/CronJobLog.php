<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Execution log row for one run of a scheduled cron job.
 * Populated by App\Services\Cron\CronJobRunner for every scheduled / manual run.
 */
class CronJobLog extends Model
{
    use HasFactory;

    public const STATUS_RUNNING = 'running';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'job_name',
        'status',
        'started_at',
        'finished_at',
        'duration',
        'records_processed',
        'records_affected',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration' => 'float',
        'records_processed' => 'integer',
        'records_affected' => 'integer',
        'metadata' => 'array',
    ];

    public function scopeJob($query, string $jobName)
    {
        return $query->where('job_name', $jobName);
    }
}
