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
        'status',
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

    // Status helpers
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'in_progress' => 'In Progress',
            'prod_complete' => 'Prod. Selesai',
            'fully_qc' => "Fully QC'd",
            default => 'Belum Produksi',
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'in_progress' => 'badge-warning',
            'prod_complete' => 'badge-info',
            'fully_qc' => 'badge-success',
            default => 'badge-gray',
        };
    }
}
