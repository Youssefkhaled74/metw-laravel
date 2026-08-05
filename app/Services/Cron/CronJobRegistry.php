<?php

namespace App\Services\Cron;

use App\Contracts\CronJob;
use App\Jobs\Scheduled\AggregateSubShipments;
use App\Jobs\Scheduled\AutoRejectOverdueOffers;
use App\Jobs\Scheduled\CancelUnpaidAdvancePayments;
use App\Jobs\Scheduled\CloseExpiredResponseWindows;
use App\Jobs\Scheduled\MarkExecutionStart;
use App\Models\CronJobLog;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

/**
 * Static definition of every cron job: stable key, class, labels/descriptions
 * (EN/AR), scheduler frequency and how to compute the next expected run.
 */
class CronJobRegistry
{
    /**
     * @var array<string, array{
     *   key: string,
     *   class: class-string<CronJob>,
     *   label_en: string,
     *   label_ar: string,
     *   description_en: string,
     *   description_ar: string,
     *   icon: string,
     *   tone: string,
     *   frequency_label_en: string,
     *   frequency_label_ar: string,
     *   interval_minutes: int,
     *   daily_run?: bool
     * }>
     */
    protected array $definitions = [
        'courier-auto-reject' => [
            'key' => 'courier-auto-reject',
            'class' => AutoRejectOverdueOffers::class,
            'label_en' => 'Auto Reject Overdue Offers',
            'label_ar' => 'رفض العروض المتأخرة تلقائيًا',
            'description_en' => 'Automatically rejects courier offers that received no response within the configured working hours (default X = 7). Working hours are 10:00–22:00 by default and only working hours count.',
            'description_ar' => 'يرفض تلقائيًا عروض المناديب التي لم تتلقَّ ردًا خلال ساعات العمل المحددة (افتراضي X = 7 ساعات). ساعات العمل من 10 صباحًا إلى 10 مساءً افتراضيًا ولا تُحتسب إلا ساعات العمل.',
            'icon' => 'fas fa-hourglass-end',
            'tone' => 'danger',
            'frequency_label_en' => 'Every minute',
            'frequency_label_ar' => 'كل دقيقة',
            'interval_minutes' => 1,
        ],
        'courier-close-response-window' => [
            'key' => 'courier-close-response-window',
            'class' => CloseExpiredResponseWindows::class,
            'label_en' => 'Close Response Window',
            'label_ar' => 'إغلاق نافذة الاستجابة',
            'description_en' => 'Once the first courier accepts, the remaining offers are limited to a response window of Y working hours (default Y = 3). This job expires the offers whose window has closed.',
            'description_ar' => 'بعد أن يقبل أول مندوب، تُقيَّد العروض المتبقية بنافذة استجابة مدتها Y ساعة عمل (افتراضي Y = 3). هذه المهمة تُنهي العروض التي أُغلقت نافذتها.',
            'icon' => 'fas fa-stopwatch',
            'tone' => 'warning',
            'frequency_label_en' => 'Every minute',
            'frequency_label_ar' => 'كل دقيقة',
            'interval_minutes' => 1,
        ],
        'advance-payments-cancel-unpaid' => [
            'key' => 'advance-payments-cancel-unpaid',
            'class' => CancelUnpaidAdvancePayments::class,
            'label_en' => 'Cancel Unpaid Advance Payments',
            'label_ar' => 'إلغاء الدفعات المقدمة غير المسددة',
            'description_en' => 'Cancels User shipment requests that stayed in awaiting-advance without paying for Z real hours (default Z = 12). Seller requests are never touched.',
            'description_ar' => 'تلغي طلبات الشحن الخاصة بالمستخدمين التي ظلت بانتظار الدفعة المقدمة دون سداد لمدة Z ساعة فعلية (افتراضي Z = 12). لا تمس طلبات البائعين إطلاقًا.',
            'icon' => 'fas fa-wallet',
            'tone' => 'danger',
            'frequency_label_en' => 'Every 15 minutes',
            'frequency_label_ar' => 'كل 15 دقيقة',
            'interval_minutes' => 15,
        ],
        'sub-shipments-aggregate' => [
            'key' => 'sub-shipments-aggregate',
            'class' => AggregateSubShipments::class,
            'label_en' => 'Aggregate Sub-Shipments',
            'label_ar' => 'تجميع الشحنات الفرعية',
            'description_en' => 'Groups open shipping requests by their receiver governorate (matched to active warehouses) every N days (default N = 3). Scope is configurable: all / one warehouse / one governorate group.',
            'description_ar' => 'تجمّع طلبات الشحن المفتوحة حسب محافظة المستلم (مطابِقة مع المستودعات النشطة) كل N يوم (افتراضي N = 3). النطاق قابل للتكوين: الكل / مستودع واحد / مجموعة محافظة واحدة.',
            'icon' => 'fas fa-layer-group',
            'tone' => 'info',
            'frequency_label_en' => 'Daily at 02:00',
            'frequency_label_ar' => 'يوميًا الساعة 02:00',
            'interval_minutes' => 1440,
            'daily_run' => true,
        ],
        'shipment-requests-mark-execution-start' => [
            'key' => 'shipment-requests-mark-execution-start',
            'class' => MarkExecutionStart::class,
            'label_en' => 'Mark Execution Start',
            'label_ar' => 'بدء التنفيذ تلقائيًا',
            'description_en' => 'Starts execution of inter-governorate requests whose advance payment was already confirmed but execution never started, after W real hours (default W = 24). It never starts unpaid requests.',
            'description_ar' => 'يبدأ تنفيذ الطلبات بين المحافظات التي تأكدت دفعتها المقدمة بالفعل لكن التنفيذ لم يبدأ، بعد مرور W ساعة فعلية (افتراضي W = 24). لا يبدأ أبدًا الطلبات غير المدفوعة.',
            'icon' => 'fas fa-play-circle',
            'tone' => 'success',
            'frequency_label_en' => 'Every 15 minutes',
            'frequency_label_ar' => 'كل 15 دقيقة',
            'interval_minutes' => 15,
        ],
    ];

    public function all(): array
    {
        $lastRuns = CronJobLog::query()
            ->whereIn('job_name', array_keys($this->definitions))
            ->get()
            ->groupBy('job_name');

        return array_map(function (array $definition) use ($lastRuns) {
            $lastRun = $lastRuns->get($definition['key'])?->sortByDesc('started_at')->first();

            return $definition + [
                'last_run' => $lastRun,
                'next_run' => $this->nextRun($definition, $lastRun),
            ];
        }, $this->definitions);
    }

    public function definition(string $key): array
    {
        if (! isset($this->definitions[$key])) {
            throw new InvalidArgumentException("Unknown cron job [{$key}].");
        }

        $lastRun = CronJobLog::query()
            ->job($key)
            ->latest('started_at')
            ->first();

        return $this->definitions[$key] + [
            'last_run' => $lastRun,
            'next_run' => $this->nextRun($this->definitions[$key], $lastRun),
        ];
    }

    public function resolve(string $key): CronJob
    {
        return app($this->definition($key)['class']);
    }

    protected function nextRun(array $definition, ?CronJobLog $lastRun): ?Carbon
    {
        if (! empty($definition['daily_run'])) {
            $next = Carbon::tomorrow()->setTime(2, 0, 0);

            if (now()->greaterThan($next)) {
                $next = $next->addDay();
            }

            return $next;
        }

        $base = $lastRun?->finished_at ?? now();

        return $base->copy()->addMinutes($definition['interval_minutes']);
    }
}
