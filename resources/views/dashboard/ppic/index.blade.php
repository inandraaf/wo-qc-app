<x-app-layout>
    <x-slot name="topbar-title">Dasbor PPIC</x-slot>
    <x-slot name="topbar-subtitle">Manajemen Work Order & Pantau Progres</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Dasbor PPIC</h1>
            <p>Buat dan pantau seluruh Work Order</p>
        </div>
        <a href="{{ route('work-orders.create') }}" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat WO
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- PPIC Stats -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#eff6ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="stat-card-value">{{ $stats['total_wo'] }}</div>
            <div class="stat-card-label">Total Work Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#f0fdf4;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($stats['total_order']) }}</div>
            <div class="stat-card-label">Total Order</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#dbeafe;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($stats['total_produced']) }}</div>
            <div class="stat-card-label">Sudah Diproduksi</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#f0fdf4;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($stats['total_qc_passed']) }}</div>
            <div class="stat-card-label">QC Passed</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef2f2;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($stats['total_qc_failed']) }}</div>
            <div class="stat-card-label">QC Rejected</div>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:0.75rem; padding:0.625rem 1rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
        <form method="GET" action="{{ route('dashboard.ppic') }}" style="display:flex; align-items:center; gap:0.25rem;">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            @if($dateFrom)<input type="hidden" name="date_from" value="{{ $dateFrom }}">@endif
            @if($dateTo)<input type="hidden" name="date_to" value="{{ $dateTo }}">@endif
            <div class="search-box" style="width:180px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari..." style="width:100%;">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm" style="padding:0.375rem 0.5rem;">Cari</button>
            @if($search)
                <a href="{{ route('dashboard.ppic', ['status' => $status, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-secondary btn-sm" style="padding:0.375rem 0.5rem;">x</a>
            @endif
        </form>

        <form method="GET" action="{{ route('dashboard.ppic') }}" style="display:flex; align-items:center; gap:0.25rem;">
            @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <span style="font-size:0.7rem; color:#94a3b8; white-space:nowrap;">Tgl:</span>
            <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="form-control" style="padding:0.25rem 0.5rem; font-size:0.75rem; width:130px;" onchange="this.form.submit()">
            <span style="font-size:0.7rem; color:#94a3b8;">—</span>
            <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="form-control" style="padding:0.25rem 0.5rem; font-size:0.75rem; width:130px;" onchange="this.form.submit()">
            @if($dateFrom || $dateTo)
                <a href="{{ route('dashboard.ppic', ['search' => $search, 'status' => $status]) }}" class="btn btn-secondary btn-sm" style="padding:0.375rem 0.5rem;">x</a>
            @endif
        </form>

        <div style="display:flex; gap:0.25rem; margin-left:auto;">
            <a href="{{ route('dashboard.ppic', ['search' => $search, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
               class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-secondary' }}">Semua</a>
            <a href="{{ route('dashboard.ppic', ['search' => $search, 'status' => 'in_progress', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
               class="btn btn-sm {{ $status === 'in_progress' ? 'btn-primary' : 'btn-secondary' }}">In Progress</a>
            <a href="{{ route('dashboard.ppic', ['search' => $search, 'status' => 'prod_complete', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
               class="btn btn-sm {{ $status === 'prod_complete' ? 'btn-primary' : 'btn-secondary' }}">Prod. Selesai</a>
            <a href="{{ route('dashboard.ppic', ['search' => $search, 'status' => 'fully_qc', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
               class="btn btn-sm {{ $status === 'fully_qc' ? 'btn-primary' : 'btn-secondary' }}">Fully QC'd</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Daftar Work Orders</span>
            <span style="font-size:0.75rem; color:#94a3b8;">{{ $workOrders->total() }} data</span>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th><a href="{{ $sortLinks['wo_number']['url'] }}" style="color:{{ $sortLinks['wo_number']['active'] ? '#2563eb' : '#64748b' }}; text-decoration:none; white-space:nowrap;">WO Number{{ $sortLinks['wo_number']['arrow'] }}</a></th>
                        <th><a href="{{ $sortLinks['product']['url'] }}" style="color:{{ $sortLinks['product']['active'] ? '#2563eb' : '#64748b' }}; text-decoration:none; white-space:nowrap;">Product{{ $sortLinks['product']['arrow'] }}</a></th>
                        <th><a href="{{ $sortLinks['date']['url'] }}" style="color:{{ $sortLinks['date']['active'] ? '#2563eb' : '#64748b' }}; text-decoration:none; white-space:nowrap;">Tanggal{{ $sortLinks['date']['arrow'] }}</a></th>
                        <th class="text-right"><a href="{{ $sortLinks['qty_order']['url'] }}" style="color:{{ $sortLinks['qty_order']['active'] ? '#2563eb' : '#64748b' }}; text-decoration:none; white-space:nowrap;">Qty Order{{ $sortLinks['qty_order']['arrow'] }}</a></th>
                        <th class="text-right">Produksi</th>
                        <th style="width:140px;">Progress</th>
                        <th class="text-right">QC Good</th>
                        <th class="text-right">QC Reject</th>
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
                            $sisaP = $wo->qty_order - $prodSum;
                            $prog = $wo->qty_order > 0 ? min(100, round($prodSum / $wo->qty_order * 100)) : 0;
                            $statusDot = match($wo->status) {
                                'in_progress' => '#f59e0b',
                                'prod_complete' => '#3b82f6',
                                'fully_qc' => '#22c55e',
                                default => '#94a3b8',
                            };
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
                                <div style="font-size:0.65rem; color:#94a3b8; margin-top:2px;">Sisa: {{ number_format(max(0, $sisaP)) }}</div>
                            </td>
                            <td class="text-right"><span class="num" style="color:#15803d; font-weight:700;">{{ number_format($qcG) }}</span></td>
                            <td class="text-right"><span class="num" style="color:#dc2626; font-weight:700;">{{ number_format($qcNG) }}</span></td>
                            <td class="text-center">
                                <span class="badge {{ $wo->status_class }}"><span class="badge-dot" style="background:{{ $statusDot }}"></span>{{ $wo->status_label }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('work-orders.show', $wo) }}" class="action-link action-link-detail">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10">
                            <div class="empty-state">
                                <div class="empty-state-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                                <div class="empty-state-title">Belum ada Work Order</div>
                                <div class="empty-state-text">@if($search || $status || $dateFrom || $dateTo)Ubah filter @elseBuat WO pertama @endif</div>
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
