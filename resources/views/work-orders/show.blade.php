<x-app-layout>
    <x-slot name="topbar-title">{{ $workOrder->wo_number }}</x-slot>
    <x-slot name="topbar-subtitle">Detail Work Order</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Detail Work Order</h1>
            <p>{{ $workOrder->wo_number }} — {{ $workOrder->product }}</p>
        </div>
        <div style="display:flex; gap:0.5rem;">
            @can('update', $workOrder)
                <a href="{{ route('work-orders.edit', $workOrder) }}" class="btn btn-secondary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                </a>
            @endcan
            <a href="{{ route('work-orders.index') }}" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @php
        $totalProduction = $workOrder->productions_sum_qty_production ?? 0;
        $totalQcGood = $workOrder->qc_total_good ?? 0;
        $totalQcNotGood = $workOrder->qc_total_not_good ?? 0;
        $totalQc = $totalQcGood + $totalQcNotGood;
        $sisaProd = $workOrder->qty_order - $totalProduction;
        $sisaQc = $totalProduction - $totalQc;
        $prodProgress = $workOrder->qty_order > 0 ? min(100, round($totalProduction / $workOrder->qty_order * 100)) : 0;
        $qcRate = $totalProduction > 0 ? round($totalQcGood / $totalProduction * 100) : 0;
    @endphp

    <!-- Info & Stats Row -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-card-label">Qty Order</div>
            <div class="stat-card-value" style="margin-top:0.375rem;">{{ number_format($workOrder->qty_order) }}</div>
            <div style="font-size:0.7rem; color:#64748b; margin-top:0.25rem;">{{ $workOrder->date->format('d M Y') }}</div>
        </div>

        <div class="stat-card" style="border-left:3px solid #2563eb;">
            <div class="stat-card-label">Total Produksi</div>
            <div class="stat-card-value" style="color:#2563eb; margin-top:0.375rem;">{{ number_format($totalProduction) }}</div>
            <div style="font-size:0.7rem; color:#64748b; margin-top:0.25rem;">{{ $prodProgress }}% dari order</div>
        </div>

        <div class="stat-card" style="border-left:3px solid #f59e0b;">
            <div class="stat-card-label">Sisa Produksi</div>
            <div class="stat-card-value" style="color:{{ $sisaProd > 0 ? '#d97706' : '#15803d' }}; margin-top:0.375rem;">
                {{ number_format(max(0, $sisaProd)) }}
            </div>
            @if($sisaProd <= 0)
                <span class="badge badge-success" style="margin-top:0.375rem;">
                    <span class="badge-dot"></span>Complete
                </span>
            @endif
        </div>

        <div class="stat-card" style="border-left:3px solid #15803d;">
            <div class="stat-card-label">QC Good</div>
            <div class="stat-card-value" style="color:#15803d; margin-top:0.375rem;">{{ number_format($totalQcGood) }}</div>
            <div style="font-size:0.7rem; color:#64748b; margin-top:0.25rem;">{{ $totalProduction > 0 ? round($totalQcGood/$totalProduction*100) : 0 }}% pass rate</div>
        </div>

        <div class="stat-card" style="border-left:3px solid #dc2626;">
            <div class="stat-card-label">QC Reject</div>
            <div class="stat-card-value" style="color:#dc2626; margin-top:0.375rem;">{{ number_format($totalQcNotGood) }}</div>
            <div style="font-size:0.7rem; color:#64748b; margin-top:0.25rem;">{{ $totalProduction > 0 ? round($totalQcNotGood/$totalProduction*100) : 0 }}% reject</div>
        </div>

        <div class="stat-card" style="border-left:3px solid #8b5cf6;">
            <div class="stat-card-label">Sisa QC</div>
            <div class="stat-card-value" style="color:{{ $sisaQc > 0 ? '#7c3aed' : '#15803d' }}; margin-top:0.375rem;">
                {{ number_format(max(0, $sisaQc)) }}
            </div>
            @if($sisaQc <= 0 && $totalProduction > 0)
                <span class="badge badge-success" style="margin-top:0.375rem;">
                    <span class="badge-dot"></span>All QC'd
                </span>
            @elseif($sisaQc > 0)
                <span class="badge badge-warning" style="margin-top:0.375rem;">
                    <span class="badge-dot"></span>Pending
                </span>
            @endif
        </div>
    </div>

    <!-- Progress -->
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-body">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
                <div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                        <span style="font-size:0.8rem; font-weight:600; color:#475569;">Progress Produksi</span>
                        <span style="font-size:0.8rem; font-weight:700; color:#0f172a;">{{ $prodProgress }}%</span>
                    </div>
                    <div class="wo-progress" style="height:8px;">
                        <div class="wo-progress-fill {{ $prodProgress >= 100 ? 'complete' : 'partial' }}"
                             style="width:{{ $prodProgress }}%;"></div>
                    </div>
                </div>
                <div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                        <span style="font-size:0.8rem; font-weight:600; color:#475569;">QC Pass Rate</span>
                        <span style="font-size:0.8rem; font-weight:700; color:#0f172a;">{{ $qcRate }}%</span>
                    </div>
                    <div class="wo-progress" style="height:8px;">
                        <div class="wo-progress-fill complete" style="width:{{ $qcRate }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
        <!-- Productions Table -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Riwayat Produksi</span>
                <span style="font-size:0.7rem; color:#94a3b8; font-weight:600;">{{ $workOrder->productions->count() }} entri</span>
            </div>
            <div class="table-wrapper" style="max-height:320px; overflow-y:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th class="text-right">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workOrder->productions as $prod)
                            <tr>
                                <td style="color:#475569; font-size:0.8rem;">{{ $prod->production_date->format('d M Y') }}</td>
                                <td class="text-right">
                                    <span class="num" style="color:#2563eb; font-weight:700;">{{ number_format($prod->qty_production) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="text-align:center; color:#94a3b8; padding:1.5rem; font-size:0.8rem;">Belum ada data produksi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- QC Table -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Riwayat Quality Control</span>
                <span style="font-size:0.7rem; color:#94a3b8; font-weight:600;">{{ $workOrder->qualityControls->count() }} entri</span>
            </div>
            <div class="table-wrapper" style="max-height:320px; overflow-y:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th class="text-right">Good</th>
                            <th class="text-right">Reject</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workOrder->qualityControls as $qc)
                            <tr>
                                <td style="color:#475569; font-size:0.8rem;">{{ $qc->qc_date->format('d M Y') }}</td>
                                <td class="text-right">
                                    <span class="num" style="color:#15803d; font-weight:700;">{{ number_format($qc->qty_good) }}</span>
                                </td>
                                <td class="text-right">
                                    <span class="num" style="color:#dc2626; font-weight:700;">{{ number_format($qc->qty_not_good) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="text-align:center; color:#94a3b8; padding:1.5rem; font-size:0.8rem;">Belum ada data QC</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
