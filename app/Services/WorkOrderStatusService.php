<?php

namespace App\Services;

use App\Models\WorkOrder;

class WorkOrderStatusService
{
    /**
     * Calculate and update status for a work order.
     * Status values: in_progress, prod_complete, fully_qc
     */
    public static function updateStatus(WorkOrder $workOrder): void
    {
        $totalProduction = $workOrder->productions()->sum('qty_production') ?? 0;
        $totalQcGood = $workOrder->qualityControls()->sum('qty_good') ?? 0;
        $totalQcBad = $workOrder->qualityControls()->sum('qty_not_good') ?? 0;
        $totalQc = $totalQcGood + $totalQcBad;

        if ($totalProduction == 0) {
            $status = 'in_progress';
        } elseif ($totalProduction < $workOrder->qty_order) {
            $status = 'in_progress';
        } elseif ($totalQc < $totalProduction) {
            $status = 'prod_complete';
        } else {
            $status = 'fully_qc';
        }

        $workOrder->update(['status' => $status]);
    }

    /**
     * Update status after a production is added.
     */
    public static function afterProduction(WorkOrder $workOrder): void
    {
        self::updateStatus($workOrder);
    }

    /**
     * Update status after a QC is recorded.
     */
    public static function afterQc(WorkOrder $workOrder): void
    {
        self::updateStatus($workOrder);
    }
}
