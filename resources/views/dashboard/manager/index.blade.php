<x-app-layout>
    <x-slot name="topbar-title">Dasbor Manager</x-slot>
    <x-slot name="topbar-subtitle">Monitoring Agregat</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>Dasbor Manager</h1>
            <p>Monitoring seluruh aktivitas produksi secara agregat</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Aggregate Stats -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#eff6ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="stat-card-value">{{ $totalWo }}</div>
            <div class="stat-card-label">Total WO</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#f0fdf4;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($totalOrder) }}</div>
            <div class="stat-card-label">Total Order</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#dbeafe;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($totalProduced) }}</div>
            <div class="stat-card-label">Diproduksi</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef9c3;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($remainingProduction) }}</div>
            <div class="stat-card-label">Sisa Produksi</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#dcfce7;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($totalQcGood) }}</div>
            <div class="stat-card-label">QC Passed</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef2f2;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div class="stat-card-value">{{ number_format($totalQcBad) }}</div>
            <div class="stat-card-label">QC Rejected</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:1.5rem; margin-bottom:1.5rem;">
        <!-- WO Status Distribution Chart -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Distribusi Status WO</span>
            </div>
            <div class="card-body" style="display:flex; justify-content:center; align-items:center; padding:1.5rem;">
                <canvas id="statusChart" width="200" height="200"></canvas>
            </div>
        </div>

        <!-- Production vs Order Progress -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Progress Produksi vs Order</span>
            </div>
            <div class="card-body" style="padding:1.5rem;">
                <!-- Order Progress -->
                <div style="margin-bottom:1.5rem;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                        <span style="font-size:0.8rem; color:#475569;">Order</span>
                        <span style="font-size:0.8rem; font-weight:600; color:#334155;">{{ number_format($totalProduced) }} / {{ number_format($totalOrder) }}</span>
                    </div>
                    <div style="background:#e2e8f0; border-radius:9999px; height:24px; overflow:hidden;">
                        <div style="background:linear-gradient(90deg, #3b82f6, #60a5fa); height:100%; width:{{ $totalOrder > 0 ? min(100, round($totalProduced / $totalOrder * 100)) : 0 }}%; display:flex; align-items:center; justify-content:center; transition:width 0.5s;">
                            @if($totalOrder > 0 && round($totalProduced / $totalOrder * 100) > 15)
                                <span style="color:white; font-size:0.75rem; font-weight:600;">{{ round($totalProduced / $totalOrder * 100) }}%</span>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- QC Progress -->
                <div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                        <span style="font-size:0.8rem; color:#475569;">QC Passed</span>
                        <span style="font-size:0.8rem; font-weight:600; color:#334155;">{{ number_format($totalQcGood) }} / {{ number_format($totalProduced) }}</span>
                    </div>
                    <div style="background:#e2e8f0; border-radius:9999px; height:24px; overflow:hidden;">
                        <div style="background:linear-gradient(90deg, #16a34a, #4ade80); height:100%; width:{{ $totalProduced > 0 ? min(100, round($totalQcGood / $totalProduced * 100)) : 0 }}%; display:flex; align-items:center; justify-content:center; transition:width 0.5s;">
                            @if($totalProduced > 0 && round($totalQcGood / $totalProduced * 100) > 15)
                                <span style="color:white; font-size:0.75rem; font-weight:600;">{{ round($totalQcGood / $totalProduced * 100) }}%</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QC Pass Rate -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">QC Pass Rate</span>
            </div>
            <div class="card-body" style="display:flex; justify-content:center; align-items:center; padding:1.5rem;">
                <canvas id="qcRateChart" width="200" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        <div class="stat-card" style="border-left:3px solid #f59e0b;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:12px; height:12px; border-radius:50%; background:#f59e0b;"></div>
                <span style="font-size:0.8rem; color:#64748b;">In Progress</span>
            </div>
            <div style="font-size:1.5rem; font-weight:700; color:#d97706; margin-top:0.5rem;">{{ $statusBreakdown['in_progress'] }}</div>
            <div style="font-size:0.7rem; color:#94a3b8;">WO sedang diproduksi</div>
        </div>
        <div class="stat-card" style="border-left:3px solid #3b82f6;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:12px; height:12px; border-radius:50%; background:#3b82f6;"></div>
                <span style="font-size:0.8rem; color:#64748b;">Prod. Selesai</span>
            </div>
            <div style="font-size:1.5rem; font-weight:700; color:#2563eb; margin-top:0.5rem;">{{ $statusBreakdown['prod_complete'] }}</div>
            <div style="font-size:0.7rem; color:#94a3b8;">Menunggu QC</div>
        </div>
        <div class="stat-card" style="border-left:3px solid #22c55e;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:12px; height:12px; border-radius:50%; background:#22c55e;"></div>
                <span style="font-size:0.8rem; color:#64748b;">Fully QC'd</span>
            </div>
            <div style="font-size:1.5rem; font-weight:700; color:#15803d; margin-top:0.5rem;">{{ $statusBreakdown['fully_qc'] }}</div>
            <div style="font-size:0.7rem; color:#94a3b8;">Selesai semua</div>
        </div>
        <div class="stat-card" style="border-left:3px solid #dc2626;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:12px; height:12px; border-radius:50%; background:#dc2626;"></div>
                <span style="font-size:0.8rem; color:#64748b;">Sisa QC</span>
            </div>
            <div style="font-size:1.5rem; font-weight:700; color:#dc2626; margin-top:0.5rem;">{{ number_format($remainingQc) }}</div>
            <div style="font-size:0.7rem; color:#94a3b8;">Belum di-QC</div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap:1.5rem;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Produksi Terbaru</span>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>WO</th>
                            <th>Tanggal</th>
                            <th class="text-right">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProductions as $p)
                            <tr>
                                <td><span style="font-weight:600; font-size:0.85rem;">{{ $p->workOrder->wo_number ?? '-' }}</span></td>
                                <td style="color:#64748b; font-size:0.8rem;">{{ $p->production_date->format('d M Y') }}</td>
                                <td class="text-right"><span class="num" style="color:#2563eb; font-weight:700;">+{{ number_format($p->qty_production) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="text-align:center; color:#94a3b8; padding:1.5rem;">Belum ada data produksi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">QC Terbaru</span>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>WO</th>
                            <th>Tanggal</th>
                            <th class="text-right">Good</th>
                            <th class="text-right">Reject</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentQc as $qc)
                            <tr>
                                <td><span style="font-weight:600; font-size:0.85rem;">{{ $qc->workOrder->wo_number ?? '-' }}</span></td>
                                <td style="color:#64748b; font-size:0.8rem;">{{ $qc->qc_date->format('d M Y') }}</td>
                                <td class="text-right"><span class="num" style="color:#16a34a; font-weight:700;">+{{ number_format($qc->qty_good) }}</span></td>
                                <td class="text-right"><span class="num" style="color:#dc2626; font-weight:700;">+{{ number_format($qc->qty_not_good) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:1.5rem;">Belum ada data QC</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // WO Status Distribution Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['In Progress', 'Prod. Selesai', 'Fully QC\'d'],
                datasets: [{
                    data: [
                        {{ $statusBreakdown['in_progress'] }},
                        {{ $statusBreakdown['prod_complete'] }},
                        {{ $statusBreakdown['fully_qc'] }}
                    ],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#22c55e'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                },
                cutout: '60%'
            }
        });
    }

    // QC Pass Rate Chart
    const qcRateCtx = document.getElementById('qcRateChart');
    if (qcRateCtx) {
        new Chart(qcRateCtx, {
            type: 'doughnut',
            data: {
                labels: ['Passed', 'Rejected'],
                datasets: [{
                    data: [{{ $totalQcGood }}, {{ $totalQcBad }}],
                    backgroundColor: ['#22c55e', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = total > 0 ? Math.round(value / total * 100) : 0;
                                return context.label + ': ' + value.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
});
</script>
@endpush
</x-app-layout>

