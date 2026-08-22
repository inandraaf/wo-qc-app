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

        $workOrders = WorkOrder::withSum('productions', 'qty_production')
                               ->withSum('qualityControls as qc_total_good', 'qty_good')
                               ->withSum('qualityControls as qc_total_not_good', 'qty_not_good')
                               ->when($search, fn($q) => $q->where('wo_number', 'like', "%{$search}%")
                                                        ->orWhere('product', 'like', "%{$search}%"))
                               ->when($status, function ($q) use ($status) {
                                   match ($status) {
                                       'in_progress' => $q->havingRaw('COALESCE(productions_sum_qty_production, 0) < qty_order'),
                                       'prod_complete' => $q->havingRaw('COALESCE(productions_sum_qty_production, 0) >= qty_order')
                                                           ->havingRaw('(COALESCE(qc_total_good, 0) + COALESCE(qc_total_not_good, 0)) < COALESCE(productions_sum_qty_production, 0)'),
                                       'fully_qc' => $q->havingRaw('(COALESCE(qc_total_good, 0) + COALESCE(qc_total_not_good, 0)) >= COALESCE(productions_sum_qty_production, 0)'),
                                       default => null,
                                   };
                               })
                               ->when(!$status, fn($q) => $q->orderBy('created_at', 'desc'))
                               ->when($status, fn($q) => $q->orderBy('date', 'desc'))
                               ->paginate(20)
                               ->withQueryString();

        return view('dashboard.index', compact('workOrders', 'search', 'status'));
    }
}
