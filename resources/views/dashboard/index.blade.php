<x-app-layout>
    <x-slot name="topbar-title">Dasbor Monitoring</x-slot>
    <x-slot name="topbar-subtitle">Real-time overview seluruh Work Order</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Dasbor Monitoring</h1>
            <p>Pantau progres seluruh Work Order secara real-time</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Stat Summary Row -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        @php
            $totalWo = $workOrders->total();
            $totalOrder = $workOrders->sum('qty_order');
            $totalProd = $workOrders->sum('productions_sum_qty_production');
            $totalQc = $workOrders->sum('qc_total_good') + $workOrders->sum('qc_total_not_good');
        @endphp

        <div class="stat-card">
            <div class="stat-card-icon" style="background:#eff6ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="stat-card-value">{{ $totalWo }}</div>
            <div class="stat-card-label">Total Work Orders</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:#f0fdf4;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($totalProd) }}</div>
            <div class="stat-card-label">Total Produksi</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef3c7;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($totalQc) }}</div>
            <div class="stat-card-label">Total QC</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef2f2;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format(max(0, $totalOrder - $totalProd)) }}</div>
            <div class="stat-card-label">Sisa Produksi</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Daftar Work Orders</span>
            @can('create', App\Models\WorkOrder::class)
                <a href="{{ route('work-orders.create') }}" class="btn btn-primary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Buat WO
                </a>
            @endcan
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>WO Number</th>
                        <th>Product</th>
                        <th>Tanggal</th>
                        <th class="text-right">Qty Order</th>
                        <th class="text-right">Produksi</th>
                        <th style="width:140px;">Progress</th>
                        <th class="text-right">QC Good</th>
                        <th class="text-right">QC Reject</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrders as $wo)
                        @php
                            $totalProduction = $wo->productions_sum_qty_production ?? 0;
                            $totalQcGood = $wo->qc_total_good ?? 0;
                            $totalQcNotGood = $wo->qc_total_not_good ?? 0;
                            $totalQc = $totalQcGood + $totalQcNotGood;
                            $sisaProduksi = $wo->qty_order - $totalProduction;
                            $sisaQc = $totalProduction - $totalQc;
                            $progress = $wo->qty_order > 0 ? min(100, round($totalProduction / $wo->qty_order * 100)) : 0;
                        @endphp
                        <tr>
                            <td>
                                <span style="font-weight:700; color:#0f172a; font-size:0.85rem;">{{ $wo->wo_number }}</span>
                            </td>
                            <td style="color:#475569;">{{ $wo->product }}</td>
                            <td>
                                <span style="color:#64748b; font-size:0.8rem;">{{ $wo->date->format('d M Y') }}</span>
                            </td>
                            <td class="text-right">
                                <span class="num" style="color:#334155;">{{ number_format($wo->qty_order) }}</span>
                            </td>
                            <td class="text-right">
                                <span class="num" style="color:#2563eb; font-weight:700;">{{ number_format($totalProduction) }}</span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <div class="wo-progress" style="flex:1;">
                                        <div class="wo-progress-fill {{ $progress >= 100 ? 'complete' : ($progress > 0 ? 'partial' : 'empty') }}"
                                             style="width: {{ $progress }}%;"></div>
                                    </div>
                                    <span style="font-size:0.7rem; font-weight:600; color:#64748b; min-width:32px; text-align:right;">
                                        {{ $progress }}%
                                    </span>
                                </div>
                                <div style="font-size:0.65rem; color:#94a3b8; margin-top:2px;">
                                    Sisa: {{ number_format(max(0, $sisaProduksi)) }}
                                </div>
                            </td>
                            <td class="text-right">
                                <span class="num" style="color:#15803d; font-weight:700;">{{ number_format($totalQcGood) }}</span>
                            </td>
                            <td class="text-right">
                                <span class="num" style="color:#dc2626; font-weight:700;">{{ number_format($totalQcNotGood) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="action-links">
                                    <a href="{{ route('work-orders.show', $wo) }}" class="action-link action-link-detail">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <div class="empty-state-title">Belum ada Work Order</div>
                                    <div class="empty-state-text">Buat Work Order pertama untuk memulai</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer" style="display:flex; justify-content:center; padding:1rem;">
            {{ $workOrders->links() }}
        </div>
    </div>
</x-app-layout>
