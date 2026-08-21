<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
