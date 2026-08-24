<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Production;
use App\Models\QualityControl;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    /**
     * Redirect to role-specific dashboard.
     */
    public function index(Request $request): RedirectResponse
    {
        $role = auth()->user()->role;

        return match($role) {
            'ppic' => redirect()->route('dashboard.ppic'),
            'operator' => redirect()->route('dashboard.operator'),
            'qc' => redirect()->route('dashboard.qc'),
            'manager' => redirect()->route('dashboard.manager'),
            'super_admin' => redirect()->route('dashboard.super_admin'),
            default => redirect('/'),
        };
    }

    /**
     * PPIC Dashboard - Work Order-focused.
     */
    public function ppic(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $sortBy = $request->query('sort_by', 'date');
        $sortDir = $request->query('sort_dir', 'desc');

        $allowedSorts = ['date', 'wo_number', 'product', 'qty_order', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'date';
        }
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        $validStatuses = ['in_progress', 'prod_complete', 'fully_qc'];

        $workOrders = WorkOrder::withSum('productions', 'qty_production')
                               ->withSum('qualityControls as qc_total_good', 'qty_good')
                               ->withSum('qualityControls as qc_total_not_good', 'qty_not_good')
                               ->when($search, fn($q) => $q->where(fn($q) => $q->where('wo_number', 'like', "%{$search}%")
                                                                               ->orWhere('product', 'like', "%{$search}%")))
                               ->when($status && in_array($status, $validStatuses), fn($q) => $q->where('status', $status))
                               ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                               ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
                               ->orderBy($sortBy, $sortDir)
                               ->paginate(20)
                               ->withQueryString();

        $sortLinks = [];
        $sortable = ['wo_number', 'product', 'date', 'qty_order'];
        foreach ($sortable as $col) {
            $newDir = ($sortBy === $col && $sortDir === 'desc') ? 'asc' : 'desc';
            $params = array_filter([
                'search' => $search,
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort_by' => $col,
                'sort_dir' => $newDir,
            ], fn($v) => $v !== null && $v !== '');
            $sortLinks[$col] = [
                'url' => route('dashboard.ppic', $params),
                'active' => $sortBy === $col,
                'dir' => $sortDir,
                'arrow' => $sortBy === $col ? ($sortDir === 'asc' ? ' ↑' : ' ↓') : '',
            ];
        }

        $stats = [
            'total_wo' => WorkOrder::count(),
            'total_order' => WorkOrder::sum('qty_order'),
            'total_produced' => Production::sum('qty_production'),
            'total_qc_passed' => QualityControl::sum('qty_good'),
            'total_qc_failed' => QualityControl::sum('qty_not_good'),
        ];

        return view('dashboard.ppic.index', compact(
            'workOrders', 'search', 'status', 'dateFrom', 'dateTo', 'sortLinks', 'stats'
        ));
    }

    /**
     * Operator Dashboard - Production-focused.
     */
    public function operator(Request $request): View
    {
        // Show WO list for operator to select (read-only)
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');

        $allowedSorts = ['date', 'wo_number', 'product', 'qty_order', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        // Operator sees all WO (to know what to produce)
        $workOrders = WorkOrder::withSum('productions', 'qty_production')
                               ->withSum('qualityControls as qc_total_good', 'qty_good')
                               ->withSum('qualityControls as qc_total_not_good', 'qty_not_good')
                               ->when($search, fn($q) => $q->where(fn($q) => $q->where('wo_number', 'like', "%{$search}%")
                                                                               ->orWhere('product', 'like', "%{$search}%")))
                               ->orderBy($sortBy, $sortDir)
                               ->paginate(20)
                               ->withQueryString();

        // Operator's own production stats
        $myProductions = Production::where('operator_id', auth()->id())->get();
        $myStats = [
            'total_produced' => $myProductions->sum('qty_production'),
            'total_entries' => $myProductions->count(),
            'last_produced' => $myProductions->max('production_date'),
        ];

        return view('dashboard.operator.index', compact(
            'workOrders', 'search', 'sortBy', 'sortDir', 'myStats'
        ));
    }

    /**
     * QC Dashboard - QC-focused.
     */
    public function qc(Request $request): View
    {
        // Show WO that have completed production but pending QC
        $search = $request->query('search');

        // Get all WO with production completed
        $workOrdersRaw = WorkOrder::withSum('productions', 'qty_production')
                               ->withSum('qualityControls as qc_total_good', 'qty_good')
                               ->withSum('qualityControls as qc_total_not_good', 'qty_not_good')
                               ->get()
                               ->filter(function ($wo) {
                                   $produced = $wo->productions_sum_qty_production ?? 0;
                                   $qcDone = ($wo->qc_total_good ?? 0) + ($wo->qc_total_not_good ?? 0);
                                   return $produced > $qcDone; // Has items waiting for QC
                               });

        // Apply search filter if any
        if ($search) {
            $workOrdersRaw = $workOrdersRaw->filter(fn($wo) =>
                str_contains(strtolower($wo->wo_number), strtolower($search)) ||
                str_contains(strtolower($wo->product ?? ''), strtolower($search))
            );
        }

        // Manual pagination on collection
        $perPage = 20;
        $total = $workOrdersRaw->count();
        $currentPage = $request->query('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $workOrders = new LengthAwarePaginator(
            $workOrdersRaw->slice($offset, $perPage)->values(),
            $total,
            $perPage,
            $currentPage,
            ['path' => route('dashboard.qc')]
        );

        // QC stats
        $qcStats = [
            'total_passed' => QualityControl::sum('qty_good'),
            'total_failed' => QualityControl::sum('qty_not_good'),
            'total_inspected' => QualityControl::count(),
            'pass_rate' => QualityControl::count() > 0
                ? round(QualityControl::sum('qty_good') / (QualityControl::sum('qty_good') + QualityControl::sum('qty_not_good')) * 100, 1)
                : 0,
        ];

        return view('dashboard.qc.index', compact('workOrders', 'search', 'qcStats'));
    }

    /**
     * Manager Dashboard - Full aggregate monitoring.
     */
    public function manager(Request $request): View
    {
        // Aggregate stats
        $totalWo = WorkOrder::count();
        $totalOrder = WorkOrder::sum('qty_order');
        $totalProduced = Production::sum('qty_production');
        $totalQcGood = QualityControl::sum('qty_good');
        $totalQcBad = QualityControl::sum('qty_not_good');

        // Calculate remaining
        $remainingProduction = max(0, $totalOrder - $totalProduced);
        $remainingQc = max(0, $totalProduced - ($totalQcGood + $totalQcBad));

        // WO status breakdown
        $statusBreakdown = [
            'total' => $totalWo,
            'in_progress' => WorkOrder::where('status', 'in_progress')->count(),
            'prod_complete' => WorkOrder::where('status', 'prod_complete')->count(),
            'fully_qc' => WorkOrder::where('status', 'fully_qc')->count(),
        ];

        // Recent activity
        $recentProductions = Production::with('workOrder')
                                                  ->orderBy('created_at', 'desc')
                                                  ->limit(5)
                                                  ->get();
        $recentQc = QualityControl::with('workOrder')
                                             ->orderBy('created_at', 'desc')
                                             ->limit(5)
                                             ->get();

        return view('dashboard.manager.index', compact(
            'totalWo', 'totalOrder', 'totalProduced', 'totalQcGood', 'totalQcBad',
            'remainingProduction', 'remainingQc', 'statusBreakdown',
            'recentProductions', 'recentQc'
        ));
    }

    /**
     * Super Admin Dashboard - Full access.
     */
    public function superAdmin(Request $request): View
    {
        // Same logic as manager but renders super_admin view
        $totalWo = WorkOrder::count();
        $totalOrder = WorkOrder::sum('qty_order');
        $totalProduced = Production::sum('qty_production');
        $totalQcGood = QualityControl::sum('qty_good');
        $totalQcBad = QualityControl::sum('qty_not_good');
        $remainingProduction = max(0, $totalOrder - $totalProduced);
        $remainingQc = max(0, $totalProduced - ($totalQcGood + $totalQcBad));

        // Use status column for breakdown (set by WorkOrderStatusService)
        $statusBreakdown = [
            'total' => $totalWo,
            'in_progress' => WorkOrder::where('status', 'in_progress')->count(),
            'prod_complete' => WorkOrder::where('status', 'prod_complete')->count(),
            'fully_qc' => WorkOrder::where('status', 'fully_qc')->count(),
        ];

        $recentProductions = Production::with('workOrder')
                                                  ->orderBy('created_at', 'desc')
                                                  ->limit(5)
                                                  ->get();
        $recentQc = QualityControl::with('workOrder')
                                             ->orderBy('created_at', 'desc')
                                             ->limit(5)
                                             ->get();

        return view('dashboard.super_admin.index', compact(
            'totalWo', 'totalOrder', 'totalProduced', 'totalQcGood', 'totalQcBad',
            'remainingProduction', 'remainingQc', 'statusBreakdown',
            'recentProductions', 'recentQc'
        ));
    }
}
