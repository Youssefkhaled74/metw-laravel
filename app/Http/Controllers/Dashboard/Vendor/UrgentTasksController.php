<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\VendorUrgentTaskService;

class UrgentTasksController extends Controller
{
    protected VendorUrgentTaskService $service;

    public function __construct(VendorUrgentTaskService $service)
    {
        $this->service = $service;
    }

    /**
     * Display all urgent tasks for the vendor
     */
    public function index(Request $request)
    {
        $vendorId = auth('vendor')->id();
        $this->service = new VendorUrgentTaskService($vendorId);

        $sort = $request->get('sort', 'priority');
        $search = $request->get('search', '');

        $data = $this->service->getAllTasks($sort, $search);

        return view('dashboard.vendor.urgent-tasks.index', [
            'tasks' => $data['tasks'],
            'stats' => $data['stats'],
            'currentSort' => $sort,
            'currentSearch' => $search,
        ]);
    }

    /**
     * AJAX: Filter tasks by type and search
     */
    public function filter(Request $request)
    {
        $vendorId = auth('vendor')->id();
        $this->service = new VendorUrgentTaskService($vendorId);

        $type = $request->get('type', 'all');
        $sort = $request->get('sort', 'priority');
        $search = $request->get('search', '');

        $data = $this->service->getAllTasks($sort, $search);

        // Filter by type if not 'all'
        if ($type !== 'all') {
            $data['tasks'] = $data['tasks']->filter(fn($t) => $t['type'] === $type)->values();
        }

        return response()->json([
            'success' => true,
            'tasks' => $data['tasks'],
            'stats' => $data['stats'],
            'html' => view('dashboard.vendor.urgent-tasks.partials.task-list', [
                'tasks' => $data['tasks'],
            ])->render(),
        ]);
    }

    /**
     * AJAX: Get updated stats only
     */
    public function stats()
    {
        $vendorId = auth('vendor')->id();
        $this->service = new VendorUrgentTaskService($vendorId);

        $stats = $this->service->getStats();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'html' => view('dashboard.vendor.urgent-tasks.partials.stats-cards', [
                'stats' => $stats,
            ])->render(),
        ]);
    }

    /**
     * AJAX: Process (accept/dismiss) a single task
     */
    public function process(Request $request)
    {
        $vendorId = auth('vendor')->id();
        $this->service = new VendorUrgentTaskService($vendorId);

        $request->validate([
            'type' => 'required|in:purchase,cancellation,return,shipping',
            'id' => 'required|integer',
        ]);

        $result = $this->service->processTask(
            $request->type,
            (int) $request->id
        );

        if ($result['success']) {
            // Refresh stats
            $stats = $this->service->getStats();

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'stats' => $stats,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'حدث خطأ أثناء معالجة المهمة',
        ], 422);
    }
}
