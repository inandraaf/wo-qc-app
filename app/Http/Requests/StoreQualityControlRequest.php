<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
