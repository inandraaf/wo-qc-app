<x-app-layout>
    <x-slot name="topbar-title">Dasbor QC</x-slot>
    <x-slot name="topbar-subtitle">Quality Control</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Dasbor Quality Control</h1>
            <p>Input hasil pemeriksaan quality control</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- QC Stats -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#dcfce7;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($qcStats['total_passed']) }}</div>
            <div class="stat-card-label">Total Passed</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef2f2;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($qcStats['total_failed']) }}</div>
            <div class="stat-card-label">Total Rejected</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#e0e7ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            </div>
            <div class="stat-card-value">{{ $qcStats['total_inspected'] }}</div>
            <div class="stat-card-label">Jumlah QC Entry</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef9c3;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <div class="stat-card-value">{{ $qcStats['pass_rate'] }}%</div>
            <div class="stat-card-label">Pass Rate</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Antrian Produksi Menunggu QC</span>
            <span style="font-size:0.75rem; color:#94a3b8;">{{ $workOrders->total() }} WO menunggu QC</span>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>WO Number</th>
                        <th>Product</th>
                        <th>Tanggal</th>
                        <th class="text-right">Qty Order</th>
                        <th class="text-right">Total Produksi</th>
                        <th class="text-right">Sudah di-QC</th>
                        <th class="text-right">Sisa QC</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrders as $wo)
                        @php
                            $prodSum = $wo->productions_sum_qty_production ?? 0;
                            $qcG = $wo->qc_total_good ?? 0;
                            $qcNG = $wo->qc_total_not_good ?? 0;
                            $qcDone = $qcG + $qcNG;
                            $sisaQc = $prodSum - $qcDone;
                        @endphp
                        <tr>
                            <td><span style="font-weight:700; color:#0f172a; font-size:0.85rem;">{{ $wo->wo_number }}</span></td>
                            <td style="color:#475569;">{{ $wo->product }}</td>
                            <td><span style="color:#64748b; font-size:0.8rem;">{{ $wo->date->format('d M Y') }}</span></td>
                            <td class="text-right"><span class="num" style="color:#334155;">{{ number_format($wo->qty_order) }}</span></td>
                            <td class="text-right"><span class="num" style="color:#2563eb; font-weight:700;">{{ number_format($prodSum) }}</span></td>
                            <td class="text-right"><span class="num" style="color:#15803d;">{{ number_format($qcDone) }}</span></td>
                            <td class="text-right"><span class="num" style="color:#dc2626; font-weight:700;">{{ number_format($sisaQc) }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('quality-controls.index', ['work_order_id' => $wo->id]) }}" class="btn btn-danger btn-sm">
                                    Input QC
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                                <div class="empty-state-title">Tidak ada antrian QC</div>
                                <div class="empty-state-text">Semua produksi sudah di-QC</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($workOrders->hasPages())
            <div class="card-footer" style="display:flex; justify-content:space-between; align-items:center; padding:0.75rem 1.25rem;">
                <span style="font-size:0.75rem; color:#94a3b8;">
                    Menampilkan <strong style="color:#334155;">{{ $workOrders->firstItem() ?? 0 }}</strong> — <strong style="color:#334155;">{{ $workOrders->lastItem() ?? 0 }}</strong> dari <strong style="color:#334155;">{{ $workOrders->total() }}</strong>
                </span>
                {{ $workOrders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
