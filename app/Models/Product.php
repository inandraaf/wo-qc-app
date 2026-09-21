<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['id', 'nama_produk'];

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

}
