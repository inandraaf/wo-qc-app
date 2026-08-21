<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkOrderRequest;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $workOrders = WorkOrder::withSum('productions', 'qty_production')
                               ->withSum('qualityControls as qc_total_good', 'qty_good')
                               ->withSum('qualityControls as qc_total_not_good', 'qty_not_good')
                               ->when($search, fn($q) => $q->where('wo_number', 'like', "%{$search}%")
                                                        ->orWhere('product', 'like', "%{$search}%"))
                               ->orderBy('created_at', 'desc')
                               ->paginate(15)
                               ->withQueryString();

        return view('work-orders.index', compact('workOrders', 'search'));
    }

    public function create(): View
    {
        return view('work-orders.create');
    }

    public function store(StoreWorkOrderRequest $request): RedirectResponse
    {
        WorkOrder::create($request->validated());

        return redirect()->route('work-orders.index')
                         ->with('success', 'Work Order berhasil dibuat.');
    }

    public function show(WorkOrder $workOrder): View
    {
        $workOrder->loadSum('productions', 'qty_production');
        $workOrder->loadSum('qualityControls as qc_total_good', 'qty_good');
        $workOrder->loadSum('qualityControls as qc_total_not_good', 'qty_not_good');
        $workOrder->load(['productions' => fn($q) => $q->orderBy('production_date', 'desc')]);
        $workOrder->load(['qualityControls' => fn($q) => $q->orderBy('qc_date', 'desc')]);

        return view('work-orders.show', compact('workOrder'));
    }

    public function edit(WorkOrder $workOrder): View
    {
        return view('work-orders.edit', compact('workOrder'));
    }

    public function update(StoreWorkOrderRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        $workOrder->update($request->validated());

        return redirect()->route('work-orders.show', $workOrder)
                         ->with('success', 'Work Order berhasil diperbarui.');
    }

    public function destroy(WorkOrder $workOrder): RedirectResponse
    {
        $workOrder->delete();

        return redirect()->route('work-orders.index')
                         ->with('success', 'Work Order berhasil dihapus.');
    }
}
