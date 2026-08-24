<x-app-layout>
    <x-slot name="topbar-title">Productions</x-slot>
    <x-slot name="topbar-subtitle">Input dan pantau hasil produksi per Work Order</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Input Produksi</h1>
            <p>Catat jumlah barang yang selesai diproduksi per Work Order</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @php
        $oldWorkOrderId = old('work_order_id');
        $oldSisa = 0;
        $oldQtyOrder = 0;
        $oldTotalProd = 0;
        if ($oldWorkOrderId) {
            $oldWo = $workOrders->firstWhere('id', $oldWorkOrderId);
            if ($oldWo) {
                $oldTotalProd = $oldWo->productions_sum_qty_production ?? 0;
                $oldSisa = $oldWo->qty_order - $oldTotalProd;
                $oldQtyOrder = $oldWo->qty_order;
            }
        }
    @endphp

    <div style="display:grid; grid-template-columns: 400px 1fr; gap:1.5rem; align-items:start;">
        <!-- Form -->
        <div class="card" x-data="{ selectedWo: '{{ $oldWorkOrderId ?? '' }}', sisa: {{ $oldSisa }}, qtyOrder: {{ $oldQtyOrder }}, totalProd: {{ $oldTotalProd }} }">
            <div class="card-header">
                <span class="card-title">Catat Produksi Baru</span>
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

                <form method="POST" action="{{ route('productions.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="work_order_id">Work Order <span style="color:#ef4444;">*</span></label>
                        <select name="work_order_id" id="work_order_id"
                                class="form-control @error('work_order_id') is-invalid @enderror"
                                required
                                x-model="selectedWo"
                                @change="
                                    selectedWo = $event.target.value;
                                    const opt = $event.target.selectedOptions[0];
                                    sisa = opt ? parseInt(opt.dataset.sisa) : 0;
                                    qtyOrder = opt ? parseInt(opt.dataset.qtyOrder) : 0;
                                    totalProd = opt ? parseInt(opt.dataset.totalProd) : 0;
                                ">
                            <option value="">-- Pilih WO --</option>
                            @foreach($workOrders as $wo)
                                @php
                                    $sisa = $wo->qty_order - ($wo->productions_sum_qty_production ?? 0);
                                @endphp
                                @if($sisa > 0)
                                    <option value="{{ $wo->id }}"
                                            data-sisa="{{ $sisa }}"
                                            data-qty-order="{{ $wo->qty_order }}"
                                            data-total-prod="{{ $wo->productions_sum_qty_production ?? 0 }}"
                                            {{ $oldWorkOrderId == $wo->id ? 'selected' : '' }}>
                                        {{ $wo->wo_number }} — {{ $wo->product }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('work_order_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sisa Info Helper -->
                    <div x-show="selectedWo !== ''"
                         x-transition
                         style="
                            background: #fef9c3;
                            border: 1px solid #fbbf24;
                            border-radius: 0.5rem;
                            padding: 0.75rem 1rem;
                            margin-bottom: 1rem;
                         ">
                        <div style="font-size:0.7rem; font-weight:600; color:#b45309; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline; vertical-align:middle; margin-right:0.25rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Info WO
                        </div>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.5rem; font-size:0.8rem;">
                            <div>
                                <span style="color:#64748b;">Qty Order</span>
                                <div style="font-weight:700; color:#0f172a;" x-text="qtyOrder.toLocaleString()"></div>
                            </div>
                            <div>
                                <span style="color:#64748b;">Total Prod.</span>
                                <div style="font-weight:700; color:#2563eb;" x-text="totalProd.toLocaleString()"></div>
                            </div>
                        </div>
                        <div style="margin-top:0.5rem; padding-top:0.5rem; border-top:1px solid #fef08a;">
                            <span style="color:#64748b; font-size:0.75rem;">Sisa boleh diproduksi</span>
                            <div style="font-size:1.1rem; font-weight:800; color:#d97706;" x-text="sisa.toLocaleString()"></div>
                        </div>
                        <div style="font-size:0.7rem; color:#b45309; margin-top:0.375rem;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline; vertical-align:middle; margin-right:0.25rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Qty produksi tidak boleh melebihi <strong x-text="sisa.toLocaleString()"></strong> unit
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="qty_production">
                            Qty Produksi <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="number" name="qty_production" id="qty_production"
                               value="{{ old('qty_production') }}"
                               min="1" step="1"
                               class="form-control @error('qty_production') is-invalid @enderror"
                               required>
                        @error('qty_production')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="production_date">
                            Tanggal Produksi <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="date" name="production_date" id="production_date"
                               value="{{ old('production_date', now()->toDateString()) }}"
                               class="form-control @error('production_date') is-invalid @enderror" required>
                        @error('production_date')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/></svg>
                        Simpan Produksi
                    </button>
                </form>
            </div>
        </div>

        <!-- Log -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Log Produksi</span>
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    @if($workOrderId)
                        <a href="{{ route('productions.index') }}" class="btn btn-secondary btn-sm">Semua</a>
                    @endif
                    <span style="font-size:0.7rem; color:#94a3b8;">{{ $productions->total() }} entri</span>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>WO Number</th>
                            <th>Product</th>
                            <th>Tanggal</th>
                            <th class="text-right">Qty</th>
                            <th>Operator</th>
                            <th class="text-right">Dicatat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productions as $prod)
                            <tr>
                                <td><span style="font-weight:700; color:#0f172a;">{{ $prod->workOrder->wo_number }}</span></td>
                                <td style="color:#475569;">{{ $prod->workOrder->product }}</td>
                                <td style="color:#64748b; font-size:0.8rem;">{{ $prod->production_date->format('d M Y') }}</td>
                                <td class="text-right">
                                    <span class="num" style="color:#2563eb; font-weight:700; font-size:0.9rem;">+{{ number_format($prod->qty_production) }}</span>
                                </td>
                                <td>
                                    @if($prod->operator)
                                        <span style="font-size:0.75rem; color:#64748b;">{{ $prod->operator->name }}</span>
                                    @else
                                        <span style="font-size:0.75rem; color:#94a3b8;">-</span>
                                    @endif
                                </td>
                                <td class="text-right" style="color:#94a3b8; font-size:0.75rem;">{{ $prod->created_at->format('d/m H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/></svg>
                                        </div>
                                        <div class="empty-state-title">Belum ada data produksi</div>
                                        <div class="empty-state-text">Catat produksi pertama melalui form di samping</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($productions->hasPages())
                <div class="card-footer" style="display:flex; justify-content:center; padding:1rem;">
                    {{ $productions->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
