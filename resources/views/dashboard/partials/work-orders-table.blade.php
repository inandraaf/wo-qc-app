{{-- Shared work orders table partial --}}
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>
                    <a href="{{ $sortLinks['wo_number']['url'] ?? '#' }}"
                       style="color:{{ $sortLinks['wo_number']['active'] ? '#2563eb' : '#64748b' }}; text-decoration:none; white-space:nowrap;">
                        WO Number{{ $sortLinks['wo_number']['arrow'] ?? '' }}
                    </a>
                </th>
                <th>
                    <a href="{{ $sortLinks['product']['url'] ?? '#' }}"
                       style="color:{{ $sortLinks['product']['active'] ? '#2563eb' : '#64748b' }}; text-decoration:none; white-space:nowrap;">
                        Product{{ $sortLinks['product']['arrow'] ?? '' }}
                    </a>
                </th>
                <th>
                    <a href="{{ $sortLinks['date']['url'] ?? '#' }}"
                       style="color:{{ $sortLinks['date']['active'] ? '#2563eb' : '#64748b' }}; text-decoration:none; white-space:nowrap;">
                        Tanggal{{ $sortLinks['date']['arrow'] ?? '' }}
                    </a>
                </th>
                <th class="text-right">
                    <a href="{{ $sortLinks['qty_order']['url'] ?? '#' }}"
                       style="color:{{ $sortLinks['qty_order']['active'] ? '#2563eb' : '#64748b' }}; text-decoration:none;">
                        Qty Order{{ $sortLinks['qty_order']['arrow'] ?? '' }}
                    </a>
                </th>
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
                    $qcTot = $qcG + $qcNG;
                    $sisaP = $wo->qty_order - $prodSum;
                    $sisaQ = $prodSum - $qcTot;
                    $prog = $wo->qty_order > 0 ? min(100, round($prodSum / $wo->qty_order * 100)) : 0;
                    if ($prodSum == 0) {
                        $lbl = 'Belum Produksi'; $cls = 'badge-gray'; $dot = '#94a3b8';
                    } elseif ($sisaP > 0) {
                        $lbl = 'In Progress'; $cls = 'badge-warning'; $dot = '#f59e0b';
                    } elseif ($sisaQ > 0) {
                        $lbl = 'Prod. Selesai'; $cls = 'badge-info'; $dot = '#3b82f6';
                    } else {
                        $lbl = "Fully QC'd"; $cls = 'badge-success'; $dot = '#22c55e';
                    }
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
                                <div class="wo-progress-fill {{ $prog >= 100 ? 'complete' : ($prog > 0 ? 'partial' : 'empty') }}"
                                     style="width:{{ $prog }}%;"></div>
                            </div>
                            <span style="font-size:0.7rem; font-weight:600; color:#64748b; min-width:32px; text-align:right;">
                                {{ $prog }}%
                            </span>
                        </div>
                        <div style="font-size:0.65rem; color:#94a3b8; margin-top:2px;">
                            Sisa: {{ number_format(max(0, $sisaP) }}
                        </div>
                    </td>
                    <td class="text-right"><span class="num" style="color:#15803d; font-weight:700;">{{ number_format($qcG) }}</span></td>
                    <td class="text-right"><span class="num" style="color:#dc2626; font-weight:700;">{{ number_format($qcNG) }}</span></td>
                    <td class="text-center">
                        <span class="badge {{ $cls }}"><span class="badge-dot" style="background:{{ $dot }}"></span>{{ $lbl }}</span>
                    </td>
                    <td class="text-center">
                        @hasSection('table-actions')
                            @yield('table-actions')
                        @else
                            <a href="{{ route('work-orders.show', $wo) }}" class="action-link action-link-detail">Detail</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="11">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="empty-state-title">
                            @if($search || $status || $dateFrom || $dateTo)
                                Ubah filter untuk melihat data lain
                            @else
                                @hasSection('empty-message')
                                    @yield('empty-message')
                                @else
                                        Belum ada data
                                @endif
                            @endif
                        </div>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>