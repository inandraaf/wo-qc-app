<x-app-layout>
    <x-slot name="topbar-title">Dasbor Operator</x-slot>
    <x-slot name="topbar-subtitle">Input Produksi</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Dasbor Operator Produksi</h1>
            <p>Input data produksi Work Order</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Operator Stats -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#dcfce7;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($myStats['total_produced']) }}</div>
            <div class="stat-card-label">Total Produksi Saya</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#e0e7ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            </div>
            <div class="stat-card-value">{{ $myStats['total_entries'] }}</div>
            <div class="stat-card-label">Jumlah Input</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef9c3;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="stat-card-value">{{ $myStats['last_produced'] ? \Carbon\Carbon::parse($myStats['last_produced'])->format('d M') : '-' }}</div>
            <div class="stat-card-label">Terakhir Produksi</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Pilih Work Order untuk Input Produksi</span>
            <span style="font-size:0.75rem; color:#94a3b8;">Klik "Input Produksi" untuk mencatat hasil</span>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>WO Number</th>
                        <th>Product</th>
                        <th>Tanggal</th>
                        <th class="text-right">Qty Order</th>
                        <th class="text-right">Sudah Diproduksi</th>
                        <th style="width:140px;">Progress</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrders as $wo)
                        @php
                            $prodSum = $wo->productions_sum_qty_production ?? 0;
                            $qcG = $wo->qc_total_good ?? 0;
                            $qcNG = $wo->qc_total_not_good ?? 0;
                            $qcTot = $qcG + $qcNG;
                            $sisaP = $wo->qty_order - $prodSum;
                            $sisaQ = $prodSum - $qcTot;
                            $prog = $wo->qty_order > 0 ? min(100, round($prodSum / $wo->qty_order * 100)) : 0;
                            if ($prodSum == 0) { $lbl = 'Belum Produksi'; $cls = 'badge-gray'; $dot = '#94a3b8';
                            } elseif ($sisaP > 0) { $lbl = 'In Progress'; $cls = 'badge-warning'; $dot = '#f59e0b';
                            } elseif ($sisaQ > 0) { $lbl = 'Siap QC'; $cls = 'badge-info'; $dot = '#3b82f6';
                            } else { $lbl = "Completed"; $cls = 'badge-success'; $dot = '#22c55e'; }
                        @endphp
                        <tr>
                            <td><span style="font-weight:700; color:#0f172a; font-size:0.85rem;">{{ $wo->wo_number }}</span></td>
                            <td style="color:#475569;">{{ $wo->product }}</td>
                            <td><span style="color:#64748b; font-size:0.8rem;">{{ $wo->date->format('d M Y') }}</span></td>
                            <td class="text-right"><span class="num" style="color:#334155;">{{ number_format($wo->qty_order) }}</span></td>
                            <td class="text-right"><span class="num" style="color:#2563eb; font-weight:700;">{{ number_format($prodSum) }}</span></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <div class="wo-progress" style="flex:1;">
                                        <div class="wo-progress-fill {{ $prog >= 100 ? 'complete' : ($prog > 0 ? 'partial' : 'empty') }}" style="width:{{ $prog }}%;"></div>
                                    </div>
                                    <span style="font-size:0.7rem; font-weight:600; color:#64748b; min-width:32px; text-align:right;">{{ $prog }}%</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $cls }}"><span class="badge-dot" style="background:{{ $dot }}"></span>{{ $lbl }}</span>
                            </td>
                            <td class="text-center">
                                @if($sisaP > 0)
                                    <a href="{{ route('productions.index', ['work_order_id' => $wo->id]) }}" class="btn btn-primary btn-sm">
                                        Input Produksi
                                    </a>
                                @else
                                    <span class="badge badge-success">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                                <div class="empty-state-title">Belum ada Work Order</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer" style="display:flex; justify-content:space-between; align-items:center; padding:0.75rem 1.25rem;">
            <span style="font-size:0.75rem; color:#94a3b8;">
                Menampilkan <strong style="color:#334155;">{{ $workOrders->firstItem() ?? 0 }}</strong> — <strong style="color:#334155;">{{ $workOrders->lastItem() ?? 0 }}</strong> dari <strong style="color:#334155;">{{ $workOrders->total() }}</strong>
            </span>
            {{ $workOrders->links() }}
        </div>
    </div>
</x-app-layout>
