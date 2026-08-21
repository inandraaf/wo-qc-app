<x-app-layout>
    <x-slot name="topbar-title">Quality Control</x-slot>
    <x-slot name="topbar-subtitle">Input hasil inspection QC — Good / Not Good</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Quality Control</h1>
            <p>Catat hasil inspection QC per Work Order</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div style="display:grid; grid-template-columns: 380px 1fr; gap:1.5rem; align-items:start;">
        <!-- Form -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Catat QC Baru</span>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-error mb-4">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div style="font-size:0.8rem;">{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('quality-controls.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="work_order_id">Work Order <span>*</span></label>
                        <select name="work_order_id" id="work_order_id" class="form-control" required>
                            <option value="">-- Pilih WO --</option>
                            @foreach($workOrders as $wo)
                                @php
                                    $totalProd = $wo->productions_sum_qty_production ?? 0;
                                    $totalQc = ($wo->qc_total_good ?? 0) + ($wo->qc_total_not_good ?? 0);
                                    $sisaQc = $totalProd - $totalQc;
                                @endphp
                                @if($sisaQc > 0)
                                    <option value="{{ $wo->id }}" {{ old('work_order_id') == $wo->id ? 'selected' : '' }}>
                                        {{ $wo->wo_number }} — {{ $wo->product }} (Sisa QC: {{ number_format($sisaQc) }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('work_order_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label" for="qty_good">
                                <span style="display:inline-flex; align-items:center; gap:0.25rem;">
                                    <span style="width:8px; height:8px; border-radius:50%; background:#22c55e; display:inline-block;"></span>
                                    Good <span>*</span>
                                </span>
                            </label>
                            <input type="number" name="qty_good" id="qty_good"
                                   value="{{ old('qty_good', 0) }}"
                                   min="0" step="1" class="form-control" required>
                            @error('qty_good')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="qty_not_good">
                                <span style="display:inline-flex; align-items:center; gap:0.25rem;">
                                    <span style="width:8px; height:8px; border-radius:50%; background:#ef4444; display:inline-block;"></span>
                                    Not Good <span>*</span>
                                </span>
                            </label>
                            <input type="number" name="qty_not_good" id="qty_not_good"
                                   value="{{ old('qty_not_good', 0) }}"
                                   min="0" step="1" class="form-control" required>
                            @error('qty_not_good')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="qc_date">Tanggal QC <span>*</span></label>
                        <input type="date" name="qc_date" id="qc_date"
                               value="{{ old('qc_date', now()->toDateString()) }}"
                               class="form-control" required>
                        @error('qc_date')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Simpan QC
                    </button>
                </form>
            </div>
        </div>

        <!-- Log -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Log Quality Control</span>
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    @if($workOrderId)
                        <a href="{{ route('quality-controls.index') }}" class="btn btn-secondary btn-sm">Semua</a>
                    @endif
                    <span style="font-size:0.7rem; color:#94a3b8;">{{ $qualityControls->total() }} entri</span>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>WO Number</th>
                            <th>Product</th>
                            <th>Tanggal</th>
                            <th class="text-right">Good</th>
                            <th class="text-right">Reject</th>
                            <th class="text-right">Dicatat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($qualityControls as $qc)
                            <tr>
                                <td><span style="font-weight:700; color:#0f172a;">{{ $qc->workOrder->wo_number }}</span></td>
                                <td style="color:#475569;">{{ $qc->workOrder->product }}</td>
                                <td style="color:#64748b; font-size:0.8rem;">{{ $qc->qc_date->format('d M Y') }}</td>
                                <td class="text-right">
                                    <span class="num" style="color:#15803d; font-weight:700;">+{{ number_format($qc->qty_good) }}</span>
                                </td>
                                <td class="text-right">
                                    <span class="num" style="color:#dc2626; font-weight:700;">+{{ number_format($qc->qty_not_good) }}</span>
                                </td>
                                <td class="text-right" style="color:#94a3b8; font-size:0.75rem;">{{ $qc->created_at->format('d/m H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        </div>
                                        <div class="empty-state-title">Belum ada data QC</div>
                                        <div class="empty-state-text">Catat QC pertama melalui form di samping</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($qualityControls->hasPages())
                <div class="card-footer" style="display:flex; justify-content:center; padding:1rem;">
                    {{ $qualityControls->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
