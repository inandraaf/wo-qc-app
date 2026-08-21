<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'wo_number' => 'required|string|max:50|unique:work_orders,wo_number',
            'date' => 'required|date',
            'product' => 'required|string|max:100',
            'qty_order' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'wo_number.unique' => 'Nomor Work Order sudah terdaftar.',
        ];
    }
}
