<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
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

        $workOrders = WorkOrder::withSum('productions', 'qty_production')
                               ->withSum('qualityControls as qc_total_good', 'qty_good')
                               ->withSum('qualityControls as qc_total_not_good', 'qty_not_good')
                               ->when($search, fn($q) => $q->where(fn($q) => $q->where('wo_number', 'like', "%{$search}%")
                                                                               ->orWhere('product', 'like', "%{$search}%")))
                               ->when($status, function ($q) use ($status) {
                                   match ($status) {
                                       'in_progress' => $q->whereRaw('COALESCE((SELECT SUM(qty_production) FROM productions WHERE work_order_id = work_orders.id), 0) < qty_order'),
                                       'prod_complete' => $q->whereRaw('COALESCE((SELECT SUM(qty_production) FROM productions WHERE work_order_id = work_orders.id), 0) >= qty_order')
                                                            ->whereRaw('(COALESCE((SELECT SUM(qty_good) FROM quality_controls WHERE work_order_id = work_orders.id), 0) + COALESCE((SELECT SUM(qty_not_good) FROM quality_controls WHERE work_order_id = work_orders.id), 0)) < COALESCE((SELECT SUM(qty_production) FROM productions WHERE work_order_id = work_orders.id), 0)'),
                                       'fully_qc' => $q->whereRaw('(COALESCE((SELECT SUM(qty_good) FROM quality_controls WHERE work_order_id = work_orders.id), 0) + COALESCE((SELECT SUM(qty_not_good) FROM quality_controls WHERE work_order_id = work_orders.id), 0)) >= COALESCE((SELECT SUM(qty_production) FROM productions WHERE work_order_id = work_orders.id), 0)'),
                                       default => null,
                                   };
                               })
                               ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                               ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
                               ->orderBy($sortBy, $sortDir)
                               ->paginate(20)
                               ->withQueryString();

        // Build sort links for table headers
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
                'url' => route('dashboard', $params),
                'active' => $sortBy === $col,
                'dir' => $sortDir,
                'arrow' => $sortBy === $col ? ($sortDir === 'asc' ? ' ↑' : ' ↓') : '',
            ];
        }

        return view('dashboard.index', compact(
            'workOrders', 'search', 'status', 'dateFrom', 'dateTo', 'sortLinks'
        ));
    }
}
