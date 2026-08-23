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
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');

        $allowedSorts = ['date', 'wo_number', 'product', 'qty_order', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        $workOrders = WorkOrder::withSum('productions', 'qty_production')
                               ->withSum('qualityControls as qc_total_good', 'qty_good')
                               ->withSum('qualityControls as qc_total_not_good', 'qty_not_good')
                               ->when($search, fn($q) => $q->where(fn($q) => $q->where('wo_number', 'like', "%{$search}%")
                                                                               ->orWhere('product', 'like', "%{$search}%")))
                               ->orderBy($sortBy, $sortDir)
                               ->paginate(15)
                               ->withQueryString();

        return view('work-orders.index', compact('workOrders', 'search', 'sortBy', 'sortDir'));
    }

    public function create(): View
    {
        $suggestedWoNumber = $this->generateWoNumber();

        return view('work-orders.create', compact('suggestedWoNumber'));
    }

    public function store(StoreWorkOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Auto-generate wo_number if left empty
        if (empty($data['wo_number'])) {
            $data['wo_number'] = $this->generateWoNumber();
        }

        WorkOrder::create($data);

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

    /**
     * Generate next WO number: WO-YYYY-NNNN
     */
    protected function generateWoNumber(): string
    {
        $year = date('Y');
        $prefix = "WO-{$year}-";

        $lastWo = WorkOrder::where('wo_number', 'like', "{$prefix}%")
                           ->orderBy('wo_number', 'desc')
                           ->first();

        if ($lastWo) {
            $lastSeq = (int) str_replace($prefix, '', $lastWo->wo_number);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }
}
