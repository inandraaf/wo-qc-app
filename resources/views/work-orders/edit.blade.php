<x-app-layout>
    <x-slot name="topbar-title">{{ $workOrder->wo_number }}</x-slot>
    <x-slot name="topbar-subtitle">Edit Work Order</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Edit Work Order</h1>
            <p>Perbarui informasi Work Order — {{ $workOrder->wo_number }}</p>
        </div>
        <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-secondary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
    </div>

    <div style="max-width:560px;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Edit Informasi Work Order</span>
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

                <form method="POST" action="{{ route('work-orders.update', $workOrder) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="wo_number">WO Number <span>*</span></label>
                        <input type="text" name="wo_number" id="wo_number"
                               value="{{ old('wo_number', $workOrder->wo_number) }}"
                               class="form-control {{ $errors->has('wo_number') ? 'is-invalid' : '' }}" required>
                        @error('wo_number')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="date">Tanggal <span>*</span></label>
                        <input type="date" name="date" id="date"
                               value="{{ old('date', $workOrder->date->toDateString()) }}"
                               class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}" required>
                        @error('date')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="product">Product <span>*</span></label>
                        <input type="text" name="product" id="product"
                               value="{{ old('product', $workOrder->product) }}"
                               class="form-control {{ $errors->has('product') ? 'is-invalid' : '' }}" required>
                        @error('product')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="qty_order">Qty Order <span>*</span></label>
                        <input type="number" name="qty_order" id="qty_order"
                               value="{{ old('qty_order', $workOrder->qty_order) }}"
                               min="0" step="1"
                               class="form-control {{ $errors->has('qty_order') ? 'is-invalid' : '' }}" required>
                        @error('qty_order')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:1.5rem;">
                        <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/></svg>
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
