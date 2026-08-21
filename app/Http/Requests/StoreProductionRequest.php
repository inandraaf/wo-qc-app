<?php

namespace App\Http\Requests;

use App\Models\WorkOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'work_order_id' => 'required|exists:work_orders,id',
            'qty_production' => 'required|integer|min:1',
            'production_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'work_order_id.exists' => 'Work Order tidak ditemukan.',
            'qty_production.min' => 'Jumlah produksi minimal 1.',
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
                                  ->find($workOrderId);

            if (!$workOrder) {
                return;
            }

            $sisaProduksi = $workOrder->qty_order - ($workOrder->productions_sum_qty_production ?? 0);
            $inputQty = (int) $this->input('qty_production', 0);

            if ($inputQty > $sisaProduksi) {
                $v->errors()->add(
                    'qty_production',
                    "Melebihi target. Sisa yang boleh diinput: {$sisaProduksi}"
                );
            }
        });
    }
}
