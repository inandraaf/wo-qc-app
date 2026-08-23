<x-app-layout>
    <x-slot name="topbar-title">Work Orders</x-slot>
    <x-slot name="topbar-subtitle">Buat Work Order baru</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Buat Work Order</h1>
            <p>Form pembuatan Work Order baru untuk produksi</p>
        </div>
        <a href="{{ route('work-orders.index') }}" class="btn btn-secondary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
    </div>

    <div style="max-width:560px;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Informasi Work Order</span>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-error mb-4">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div>
                            <strong>Ada kesalahan input:</strong>
                            <ul style="margin:0.25rem 0 0 1.25rem;">
                                @foreach ($errors->all() as $error)
                                    <li style="font-size:0.8rem;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('work-orders.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="wo_number">
                            WO Number <span style="font-size:0.65rem; font-weight:400; color:#94a3b8; text-transform:uppercase; letter-spacing:0.03em;">(auto)</span>
                        </label>
                        <input type="text" name="wo_number" id="wo_number"
                               value="{{ old('wo_number', $suggestedWoNumber) }}"
                               class="form-control @error('wo_number') is-invalid @enderror"
                               style="background:#f8fafc;" readonly>
                        @error('wo_number')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="date">
                            Tanggal <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="date" name="date" id="date"
                               value="{{ old('date', now()->toDateString()) }}"
                               class="form-control @error('date') is-invalid @enderror" required>
                        @error('date')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="product">
                            Product <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="product" id="product"
                               value="{{ old('product') }}"
                               class="form-control @error('product') is-invalid @enderror"
                               placeholder="Nama produk yang diproduksi" required>
                        @error('product')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="qty_order">
                            Qty Order <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="number" name="qty_order" id="qty_order"
                               value="{{ old('qty_order') }}"
                               min="0" step="1"
                               class="form-control @error('qty_order') is-invalid @enderror"
                               placeholder="Jumlah target produksi" required>
                        @error('qty_order')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:1.5rem;">
                        <a href="{{ route('work-orders.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/></svg>
                            Simpan Work Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
