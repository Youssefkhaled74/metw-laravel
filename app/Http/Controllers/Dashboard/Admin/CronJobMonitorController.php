<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\CronJobLog;
use App\Services\Cron\CronJobRegistry;
use App\Services\Cron\CronJobRunner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CronJobMonitorController extends Controller
{
    public function __construct(
        protected CronJobRegistry $registry,
        protected CronJobRunner $runner
    ) {
        $this->middleware('admin');
    }

    public function index()
    {
        if (Auth::guard('employee')->check()
            && ! Auth::guard('employee')->user()->can('admin.cron-jobs.index')) {
            return view('dashboard.admin.no-permission');
        }

        $jobs = collect($this->registry->all());

        $todayStart = now()->startOfDay();

        $totals = DB::table('cron_job_logs')
            ->where('started_at', '>=', $todayStart)
            ->selectRaw('count(*) as total')
            ->selectRaw("sum(case when status = 'success' then 1 else 0 end) as success")
            ->selectRaw("sum(case when status = 'failed' then 1 else 0 end) as failed")
            ->first();

        $overview = [
            'total_jobs' => $jobs->count(),
            'runs_today' => (int) ($totals->total ?? 0),
            'success_today' => (int) ($totals->success ?? 0),
            'failed_today' => (int) ($totals->failed ?? 0),
            'last_run' => CronJobLog::query()->latest('started_at')->first(),
            'next_run' => $jobs->pluck('next_run')->filter()->min(),
        ];

        $jobStats = $this->attachStats($jobs);

        return view('dashboard.admin.cron-jobs.index', [
            'jobs' => $jobStats,
            'overview' => $overview,
        ]);
    }

    public function show(Request $request, string $job)
    {
        if (Auth::guard('employee')->check()
            && ! Auth::guard('employee')->user()->can('admin.cron-jobs.show')) {
            return view('dashboard.admin.no-permission');
        }

        try {
            $definition = $this->registry->definition($job);
        } catch (\InvalidArgumentException) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => ['nullable', 'in:running,success,failed'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $query = CronJobLog::query()->job($job)->latest('started_at');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['from'])) {
            $query->whereDate('started_at', '>=', $validated['from']);
        }

        if (! empty($validated['to'])) {
            $query->whereDate('started_at', '<=', $validated['to']);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('dashboard.admin.cron-jobs.show', [
            'definition' => $definition,
            'logs' => $logs,
            'filters' => $validated,
            'successRate7d' => $this->successRate($job, 7),
            'successRate30d' => $this->successRate($job, 30),
            'runsToday' => CronJobLog::query()
                ->job($job)
                ->where('started_at', '>=', now()->startOfDay())
                ->count(),
        ]);
    }

    public function runNow(string $job)
    {
        if (Auth::guard('employee')->check()
            && ! Auth::guard('employee')->user()->can('admin.cron-jobs.run-now')) {
            return view('dashboard.admin.no-permission');
        }

        try {
            $log = $this->runner->runByKey($job);
        } catch (\InvalidArgumentException) {
            abort(404);
        }

        if ($log->status === CronJobLog::STATUS_SUCCESS) {
            return redirect()->route('admin.cron-jobs.show', $job)
                ->with('success', "Cron job ran successfully. {$log->records_affected} record(s) affected.");
        }

        return redirect()->route('admin.cron-jobs.show', $job)
            ->with('error', "Cron job failed: " . ($log->error_message ?? 'Unknown error.'));
    }

    protected function attachStats($jobs)
    {
        return $jobs->map(function (array $definition) {
            $key = $definition['key'];

            $definition['success_rate_7d'] = $this->successRate($key, 7);
            $definition['success_rate_30d'] = $this->successRate($key, 30);
            $definition['runs_today'] = CronJobLog::query()
                ->job($key)
                ->where('started_at', '>=', now()->startOfDay())
                ->count();

            return $definition;
        })->values();
    }

    protected function successRate(string $job, int $days): ?int
    {
        $since = now()->subDays($days);

        $totals = DB::table('cron_job_logs')
            ->where('job_name', $job)
            ->where('started_at', '>=', $since)
            ->selectRaw('count(*) as total')
            ->selectRaw("sum(case when status = 'success' then 1 else 0 end) as success")
            ->first();

        if ((int) ($totals->total ?? 0) === 0) {
            return null;
        }

        return (int) round(((int) ($totals->success ?? 0) / (int) $totals->total) * 100);
    }
}
