<?php

namespace App\Jobs\Scheduled;

use App\Contracts\CronJob;
use App\Enum\ShipmentRequestStatus;
use App\Models\ShipmentRequest;
use App\Models\Warehouse;
use App\Services\CourierSystem\CourierSystemConfigService;
use App\Services\Cron\CronJobResult;
use Illuminate\Support\Facades\DB;

/**
 * Cron job 4 (N days): aggregates open sub-shipments destined to receiver
 * warehouses. Open requests (submitted / matching / awaiting-path-selection)
 * are grouped by the receiver governorate matched with active warehouses, and
 * each group of two or more requests is tagged with a shared aggregation batch.
 *
 * Scope is configurable:
 *   - all      : every governorate that has at least one active warehouse
 *   - warehouse: only requests whose receiver governorate belongs to one warehouse
 *   - group    : only requests whose receiver governorate equals one governorate
 */
class AggregateSubShipments implements CronJob
{
    public function __construct(
        protected CourierSystemConfigService $config
    ) {}

    public function name(): string
    {
        return 'sub-shipments-aggregate';
    }

    public function handle(): CronJobResult
    {
        $days = $this->config->aggregateSubShipmentsDays();
        $scope = $this->config->aggregationScope();
        $targetId = $this->config->aggregationTargetId();
        $cutoff = now()->subDays($days);

        $warehouses = Warehouse::query()
            ->whereNotNull('governorate_id')
            ->get(['id', 'governorate_id']);

        $warehousesByGovernorate = $warehouses->groupBy('governorate_id');

        $allowedGovernorateIds = $this->resolveGovernorateScope($scope, $targetId, $warehousesByGovernorate);

        if ($allowedGovernorateIds->isEmpty()) {
            return new CronJobResult(0, 0, [
                'scope' => $scope,
                'target_id' => $targetId,
                'reason' => 'No receiver governorate matched the configured scope.',
            ]);
        }

        $candidates = ShipmentRequest::query()
            ->with(['receiverContact.primaryAddress'])
            ->whereIn('status', [
                ShipmentRequestStatus::SUBMITTED->value,
                ShipmentRequestStatus::MATCHING->value,
                ShipmentRequestStatus::AWAITING_PATH_SELECTION->value,
            ])
            ->whereRaw('COALESCE(submitted_at, created_at) <= ?', [$cutoff])
            ->get();

        $processed = 0;
        $grouped = collect();

        foreach ($candidates as $request) {
            $governorateId = $request->receiverContact?->primaryAddress?->governorate_id;

            if ($governorateId === null || ! $allowedGovernorateIds->contains($governorateId)) {
                continue;
            }

            $processed++;
            $grouped->put($governorateId, $grouped->get($governorateId, collect())->push($request));
        }

        $affected = 0;
        $batches = [];

        foreach ($grouped as $governorateId => $requests) {
            if ($requests->count() < 2) {
                continue;
            }

            $warehouseIds = $warehousesByGovernorate->get($governorateId)?->pluck('id')->values()->all() ?? [];
            $batch = 'agg-' . $governorateId . '-' . now()->format('Ymd-His');

            foreach ($requests as $request) {
                $request->update([
                    'metadata' => array_merge((array) $request->metadata, [
                        'aggregation_batch' => $batch,
                        'aggregation_warehouse_ids' => $warehouseIds,
                        'aggregation_created_at' => now()->toDateTimeString(),
                    ]),
                ]);
                $affected++;
            }

            $batches[] = [
                'batch' => $batch,
                'governorate_id' => $governorateId,
                'warehouse_ids' => $warehouseIds,
                'request_ids' => $requests->pluck('id')->values()->all(),
            ];
        }

        return new CronJobResult($processed, $affected, [
            'scope' => $scope,
            'target_id' => $targetId,
            'days' => $days,
            'batches' => $batches,
        ]);
    }

    protected function resolveGovernorateScope(string $scope, ?int $targetId, $warehousesByGovernorate)
    {
        if ($scope === 'warehouse') {
            $warehouse = Warehouse::query()->find($targetId);

            return $warehouse?->governorate_id ? collect([$warehouse->governorate_id]) : collect();
        }

        if ($scope === 'group') {
            return $targetId ? collect([$targetId]) : collect();
        }

        return $warehousesByGovernorate->keys();
    }
}
