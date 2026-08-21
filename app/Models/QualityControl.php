<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityControl extends Model
{
    protected $fillable = [
        'work_order_id',
        'qty_good',
        'qty_not_good',
        'qc_date',
    ];

    protected function casts(): array
    {
        return [
            'qc_date' => 'date',
            'qty_good' => 'integer',
            'qty_not_good' => 'integer',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
