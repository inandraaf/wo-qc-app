<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRequest;
use App\Models\Production;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductionController extends Controller
{
    public function index(Request $request): View
    {
        $workOrderId = $request->query('work_order_id');

        $workOrders = WorkOrder::withSum('productions', 'qty_production')
                               ->orderBy('created_at', 'desc')
                               ->get();

        $productions = Production::with('workOrder')
                                 ->when($workOrderId, fn($q) => $q->where('work_order_id', $workOrderId))
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(20)
                                 ->withQueryString();

        return view('productions.index', compact('productions', 'workOrders', 'workOrderId'));
    }

    public function store(StoreProductionRequest $request): RedirectResponse
    {
        Production::create($request->validated());

        return redirect()->back()
                         ->with('success', 'Data produksi berhasil dicatat.');
    }
}
