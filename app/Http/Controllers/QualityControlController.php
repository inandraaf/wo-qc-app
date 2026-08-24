<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQualityControlRequest;
use App\Models\QualityControl;
use App\Models\WorkOrder;
use App\Services\WorkOrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QualityControlController extends Controller
{
    public function index(Request $request): View
    {
        $workOrderId = $request->query('work_order_id');

        $workOrders = WorkOrder::withSum('productions', 'qty_production')
                               ->withSum('qualityControls as qc_total_good', 'qty_good')
                               ->withSum('qualityControls as qc_total_not_good', 'qty_not_good')
                               ->orderBy('created_at', 'desc')
                               ->get();

        $qualityControls = QualityControl::with(['workOrder', 'qcBy'])
                                         ->when($workOrderId, fn($q) => $q->where('work_order_id', $workOrderId))
                                         ->orderBy('created_at', 'desc')
                                         ->paginate(20)
                                         ->withQueryString();

        return view('quality-controls.index', compact('qualityControls', 'workOrders', 'workOrderId'));
    }

    public function store(StoreQualityControlRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $workOrder = WorkOrder::withSum('productions', 'qty_production')
                              ->withSum('qualityControls as qc_total_good', 'qty_good')
                              ->withSum('qualityControls as qc_total_not_good', 'qty_not_good')
                              ->find($data['work_order_id']);

        $totalProduksi = $workOrder->productions_sum_qty_production ?? 0;
        $totalQc = ($workOrder->qc_total_good ?? 0) + ($workOrder->qc_total_not_good ?? 0);
        $sisaQc = $totalProduksi - $totalQc;
        $inputTotal = (int) $data['qty_good'] + (int) $data['qty_not_good'];

        if ($sisaQc <= 0) {
            return redirect()->back()
                             ->withErrors(['qty_good' => 'Tidak ada produksi yang bisa di-QC. Sisa QC: 0.'])
                             ->withInput();
        }

        if ($inputTotal > $sisaQc) {
            return redirect()->back()
                             ->withErrors(['qty_good' => "Melebihi total produksi. Sisa yang boleh di-QC: {$sisaQc}"])
                             ->withInput();
        }

        // Auto-fill qc_by from authenticated user
        $data['qc_by'] = auth()->id();

        QualityControl::create($data);

        // Auto-update WO status
        WorkOrderStatusService::afterQc($workOrder);

        return redirect()->back()
                         ->with('success', 'Data QC berhasil dicatat.');
    }
}
