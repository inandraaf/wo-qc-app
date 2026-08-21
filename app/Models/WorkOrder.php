<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    protected $fillable = [
        'wo_number',
        'date',
        'product',
        'qty_order',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'qty_order' => 'integer',
        ];
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }

    public function qualityControls(): HasMany
    {
        return $this->hasMany(QualityControl::class);
    }
}
