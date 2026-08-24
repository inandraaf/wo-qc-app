<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')
                  ->constrained('work_orders')
                  ->onDelete('cascade');
            $table->foreignId('qc_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->integer('qty_good')->default(0);
            $table->integer('qty_not_good')->default(0);
            $table->date('qc_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_controls');
    }
};
