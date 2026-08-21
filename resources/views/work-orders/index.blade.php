<x-app-layout>
    <x-slot name="topbar-title">Work Orders</x-slot>
    <x-slot name="topbar-subtitle">Kelola seluruh Work Order produksi</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Work Orders</h1>
            <p>Daftar seluruh Work Order — buat, edit, dan pantau progres</p>
        </div>
        @can('create', App\Models\WorkOrder::class)
            <a href="{{ route('work-orders.create') }}" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Buat WO Baru
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4" style="padding:1rem 1.25rem; display:flex; align-items:center; gap:0.75rem;">
        <form method="GET" action="{{ route('work-orders.index') }}" style="display:flex; align-items:center; gap:0.5rem; flex:1;">
            <div class="search-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari WO Number atau Product...">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
            @if($search)
                <a href="{{ route('work-orders.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
        <div style="font-size:0.75rem; color:#94a3b8;">
            {{ $workOrders->total() }} data
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>WO Number</th>
                        <th>Product</th>
                        <th>Tanggal</th>
                        <th class="text-right">Qty Order</th>
                        <th class="text-right">Produksi</th>
                        <th style="width:120px;">Sisa Prod.</th>
                        <th class="text-right">QC</th>
                        <th style="width:120px;">Sisa QC</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrders as $wo)
                        @php
                            $totalProduction = $wo->productions_sum_qty_production ?? 0;
                            $totalQc = ($wo->qc_total_good ?? 0) + ($wo->qc_total_not_good ?? 0);
                            $sisaProd = $wo->qty_order - $totalProduction;
                            $sisaQc = $totalProduction - $totalQc;
                        @endphp
                        <tr>
                            <td><span style="font-weight:700; color:#0f172a;">{{ $wo->wo_number }}</span></td>
                            <td style="color:#475569;">{{ $wo->product }}</td>
                            <td><span style="font-size:0.8rem; color:#64748b;">{{ $wo->date->format('d M Y') }}</span></td>
                            <td class="text-right"><span class="num">{{ number_format($wo->qty_order) }}</span></td>
                            <td class="text-right">
                                <span class="num" style="color:#2563eb; font-weight:700;">{{ number_format($totalProduction) }}</span>
                            </td>
                            <td>
                                @if($sisaProd > 0)
                                    <span class="badge badge-warning">
                                        <span class="badge-dot"></span>
                                        {{ number_format($sisaProd) }}
                                    </span>
                                @else
                                    <span class="badge badge-success">
                                        <span class="badge-dot"></span>
                                        0
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <span class="num" style="color:#15803d; font-weight:700;">{{ number_format($totalQc) }}</span>
                            </td>
                            <td>
                                @if($sisaQc > 0)
                                    <span class="badge badge-warning">
                                        <span class="badge-dot"></span>
                                        {{ number_format($sisaQc) }}
                                    </span>
                                @elseif($totalProduction > 0)
                                    <span class="badge badge-success">
                                        <span class="badge-dot"></span>
                                        0
                                    </span>
                                @else
                                    <span class="badge badge-gray">
                                        <span class="badge-dot"></span>
                                        —
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="action-links">
                                    <a href="{{ route('work-orders.show', $wo) }}" class="action-link action-link-detail">Detail</a>
                                    @can('update', $wo)
                                        <a href="{{ route('work-orders.edit', $wo) }}" class="action-link action-link-edit">Edit</a>
                                    @endcan
                                    @can('delete', $wo)
                                        <form method="POST" action="{{ route('work-orders.destroy', $wo) }}" class="inline"
                                              onsubmit="return confirm('Hapus Work Order ini? Data produksi & QC juga akan terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-link-delete">Hapus</button>
                                        </form>
                                    @endcan
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
                                    <div class="empty-state-title">
                                        {{ isset($search) ? 'Pencarian tidak ditemukan' : 'Belum ada Work Order' }}
                                    </div>
                                    <div class="empty-state-text">
                                        {{ isset($search) ? 'Coba kata kunci lain' : 'Buat Work Order pertama' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($workOrders->hasPages())
            <div class="card-footer" style="display:flex; justify-content:center; padding:1rem;">
                {{ $workOrders->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
