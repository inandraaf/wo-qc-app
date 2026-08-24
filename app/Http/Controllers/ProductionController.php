<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRequest;
use App\Models\Production;
use App\Models\WorkOrder;
use App\Services\WorkOrderStatusService;
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

        $productions = Production::with(['workOrder', 'operator'])
                                 ->when($workOrderId, fn($q) => $q->where('work_order_id', $workOrderId))
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(20)
                                 ->withQueryString();

        return view('productions.index', compact('productions', 'workOrders', 'workOrderId'));
    }

    public function store(StoreProductionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $workOrder = WorkOrder::withSum('productions', 'qty_production')
                              ->find($data['work_order_id']);

        $sisa = $workOrder->qty_order - ($workOrder->productions_sum_qty_production ?? 0);

        if ((int) $data['qty_production'] > $sisa) {
            return redirect()->back()
                             ->withErrors(['qty_production' => "Melebihi target. Sisa yang boleh diinput: {$sisa}"])
                             ->withInput();
        }

        // Auto-fill operator_id from authenticated user
        $data['operator_id'] = auth()->id();

        Production::create($data);

        // Auto-update WO status
        WorkOrderStatusService::afterProduction($workOrder);

        return redirect()->back()
                         ->with('success', 'Data produksi berhasil dicatat.');
    }
}
