<?php

namespace App\Http\Requests;

use App\Models\WorkOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreQualityControlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'work_order_id' => 'required|exists:work_orders,id',
            'qty_good' => 'required|integer|min:0',
            'qty_not_good' => 'required|integer|min:0',
            'qc_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'work_order_id.exists' => 'Work Order tidak ditemukan.',
            'qty_good.min' => 'Jumlah good minimal 0.',
            'qty_not_good.min' => 'Jumlah not good minimal 0.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $workOrderId = $this->input('work_order_id');

            if (!$workOrderId || $v->errors()->has('work_order_id')) {
                return;
            }

            $workOrder = WorkOrder::withSum('productions', 'qty_production')
                                  ->withSum('qualityControls as qc_total', 'qty_good')
                                  ->withSum('qualityControls as qc_total_not', 'qty_not_good')
                                  ->find($workOrderId);

            if (!$workOrder) {
                return;
            }

            $totalProduksi = $workOrder->productions_sum_qty_production ?? 0;
            $totalQc = ($workOrder->qc_total ?? 0) + ($workOrder->qc_total_not ?? 0);
            $sisaQc = $totalProduksi - $totalQc;
            $inputQty = (int) $this->input('qty_good', 0) + (int) $this->input('qty_not_good', 0);

            if ($sisaQc <= 0) {
                $v->errors()->add(
                    'qty_good',
                    'Tidak ada produksi yang bisa di-QC. Sisa QC: 0.'
                );
                return;
            }

            if ($inputQty > $sisaQc) {
                $v->errors()->add(
                    'qty_good',
                    "Melebihi total produksi. Sisa yang boleh di-QC: {$sisaQc}"
                );
            }
        });
    }
}
