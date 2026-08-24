<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')
                  ->constrained('work_orders')
                  ->onDelete('cascade');
            $table->foreignId('operator_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->integer('qty_production');
            $table->date('production_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
