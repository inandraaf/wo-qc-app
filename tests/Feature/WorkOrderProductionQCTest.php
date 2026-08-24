<?php

namespace Tests\Feature;

use App\Models\WorkOrder;
use App\Models\Production;
use App\Models\QualityControl;
use App\Models\User;
use App\Services\WorkOrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderProductionQCTest extends TestCase
{
    use RefreshDatabase;

    protected User $ppic;
    protected User $operator;
    protected User $qc;
    protected User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ppic = User::factory()->create(['role' => 'ppic']);
        $this->operator = User::factory()->create(['role' => 'operator']);
        $this->qc = User::factory()->create(['role' => 'qc']);
        $this->manager = User::factory()->create(['role' => 'manager']);
    }

    // Work Order
    public function test_pic_can_create_work_order(): void
    {
        $this->actingAs($this->ppic)->post('/work-orders', [
            'date' => '2024-01-15',
            'product' => 'Test Product A',
            'qty_order' => 100,
        ])->assertRedirect('/work-orders');
    }

    public function test_wo_number_unique(): void
    {
        $this->actingAs($this->ppic)->post('/work-orders', [
            'wo_number' => 'WO-DUP-001',
            'date' => '2024-01-15',
            'product' => 'Dup Product',
            'qty_order' => 50,
        ]);
        $this->post('/work-orders', [
            'wo_number' => 'WO-DUP-001',
            'date' => '2024-01-16',
            'product' => 'Dup Product 2',
            'qty_order' => 30,
        ])->assertSessionHasErrors('wo_number');
    }

    public function test_auto_wo_number_when_empty(): void
    {
        $this->actingAs($this->ppic)->post('/work-orders', [
            'date' => '2024-01-15',
            'product' => 'Auto Number',
            'qty_order' => 50,
        ])->assertRedirect();
        $wo = WorkOrder::where('product', 'Auto Number')->first();
        $this->assertStringStartsWith('WO-', $wo->wo_number);
    }

    // Production
    public function test_operator_can_input_production(): void
    {
        $wo = $this->createWo(100);
        $this->actingAs($this->operator)->post('/productions', [
            'work_order_id' => $wo->id,
            'qty_production' => 30,
            'production_date' => now()->toDateString(),
        ])->assertRedirect();
    }

    public function test_production_exceed_order_blocked(): void
    {
        $wo = $this->createWo(50);
        $this->actingAs($this->operator)->post('/productions', [
            'work_order_id' => $wo->id,
            'qty_production' => 100,
            'production_date' => now()->toDateString(),
        ])->assertSessionHasErrors('qty_production');
    }

    public function test_production_partial_ok(): void
    {
        $wo = $this->createWo(100);
        $this->actingAs($this->operator)->post('/productions', [
            'work_order_id' => $wo->id,
            'qty_production' => 30,
            'production_date' => now()->toDateString(),
        ])->assertSessionHasNoErrors();
    }

    public function test_operator_id_tracked(): void
    {
        $wo = $this->createWo(100);
        $this->actingAs($this->operator)->post('/productions', [
            'work_order_id' => $wo->id,
            'qty_production' => 25,
            'production_date' => now()->toDateString(),
        ]);
        $prod = Production::first();
        $this->assertEquals($this->operator->id, $prod->operator_id);
    }

    // QC
    public function test_qc_can_input(): void
    {
        $wo = $this->createWoWithProd(100, 50);
        $this->actingAs($this->qc)->post('/quality-controls', [
            'work_order_id' => $wo->id,
            'qty_good' => 45,
            'qty_not_good' => 5,
            'qc_date' => now()->toDateString(),
        ])->assertRedirect();
    }

    public function test_qc_exceed_production_blocked(): void
    {
        $wo = $this->createWoWithProd(100, 20);
        $this->actingAs($this->qc)->post('/quality-controls', [
            'work_order_id' => $wo->id,
            'qty_good' => 25,
            'qty_not_good' => 0,
            'qc_date' => now()->toDateString(),
        ])->assertSessionHasErrors('qty_good');
    }

    public function test_qc_partial_ok(): void
    {
        $wo = $this->createWoWithProd(100, 80);
        $this->actingAs($this->qc)->post('/quality-controls', [
            'work_order_id' => $wo->id,
            'qty_good' => 30,
            'qty_not_good' => 10,
            'qc_date' => now()->toDateString(),
        ])->assertSessionHasNoErrors();
    }

    public function test_qc_by_tracked(): void
    {
        $wo = $this->createWoWithProd(100, 50);
        $this->actingAs($this->qc)->post('/quality-controls', [
            'work_order_id' => $wo->id,
            'qty_good' => 40,
            'qty_not_good' => 10,
            'qc_date' => now()->toDateString(),
        ]);
        $qc = QualityControl::first();
        $this->assertEquals($this->qc->id, $qc->qc_by);
    }

    // Status
    public function test_status_in_progress_no_prod(): void
    {
        $wo = WorkOrder::create([
            'wo_number' => 'WO-STATUS-1',
            'date' => now()->toDateString(),
            'product' => 'T',
            'qty_order' => 100,
        ]);
        WorkOrderStatusService::updateStatus($wo);
        $this->assertEquals('in_progress', $wo->fresh()->status);
    }

    public function test_status_prod_complete(): void
    {
        $wo = $this->createWoWithProd(100, 100);
        WorkOrderStatusService::updateStatus($wo->fresh());
        $this->assertEquals('prod_complete', $wo->fresh()->status);
    }

    public function test_status_fully_qc(): void
    {
        $wo = $this->createWoWithProd(100, 100);
        QualityControl::create([
            'work_order_id' => $wo->id,
            'qc_by' => $this->qc->id,
            'qty_good' => 100,
            'qty_not_good' => 0,
            'qc_date' => now()->toDateString(),
        ]);
        WorkOrderStatusService::updateStatus($wo->fresh());
        $this->assertEquals('fully_qc', $wo->fresh()->status);
    }

    // Cascade
    public function test_prod_cascade_delete(): void
    {
        $wo = $this->createWoWithProd(100, 50);
        $prodId = $wo->productions->first()->id;
        $wo->delete();
        $this->assertNull(Production::find($prodId));
    }

    public function test_qc_cascade_delete(): void
    {
        $wo = $this->createWoWithProd(100, 100);
        QualityControl::create([
            'work_order_id' => $wo->id,
            'qc_by' => $this->qc->id,
            'qty_good' => 100,
            'qty_not_good' => 0,
            'qc_date' => now()->toDateString(),
        ]);
        $qcId = $wo->fresh()->qualityControls->first()->id;
        $wo->delete();
        $this->assertNull(QualityControl::find($qcId));
    }

    // RBAC
    public function test_operator_no_wo_create(): void
    {
        $this->actingAs($this->operator)->get('/work-orders/create')->assertForbidden();
    }

    public function test_pic_cannot_prod(): void
    {
        $wo = $this->createWo(100);
        $this->actingAs($this->ppic)->post('/productions', [
            'work_order_id' => $wo->id,
            'qty_production' => 10,
            'production_date' => now()->toDateString(),
        ])->assertForbidden();
    }

    public function test_manager_blocked_all(): void
    {
        $this->actingAs($this->manager)->post('/work-orders', [
            'date' => now()->toDateString(),
            'product' => 'X',
            'qty_order' => 10,
        ])->assertForbidden();
    }

    // Helpers
    protected function createWo(int $qty): WorkOrder
    {
        return WorkOrder::create([
            'wo_number' => 'WO-'.uniqid(),
            'date' => now()->toDateString(),
            'product' => 'T',
            'qty_order' => $qty,
        ]);
    }

    protected function createWoWithProd(int $order, int $produced): WorkOrder
    {
        $wo = $this->createWo($order);
        Production::create([
            'work_order_id' => $wo->id,
            'operator_id' => $this->operator->id,
            'qty_production' => $produced,
            'production_date' => now()->toDateString(),
        ]);
        return $wo->fresh();
    }
}