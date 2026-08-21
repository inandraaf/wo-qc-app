<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $workOrders = WorkOrder::withSum('productions', 'qty_production')
                               ->withSum('qualityControls as qc_total_good', 'qty_good')
                               ->withSum('qualityControls as qc_total_not_good', 'qty_not_good')
                               ->orderBy('date', 'desc')
                               ->paginate(20);

        return view('dashboard.index', compact('workOrders'));
    }
}
