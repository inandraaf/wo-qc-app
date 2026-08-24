<x-app-layout>
    <x-slot name="topbar-title">User Management</x-slot>
    <x-slot name="topbar-subtitle">Kelola Akun User</x-slot>

    <div class="page-header">
        <div class="page-header-text">
            <h1>User Management</h1>
            <p>Kelola akun user sistem</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah User
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error mb-4">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <span class="card-title">Daftar User</span>
            <span style="font-size:0.75rem; color:#94a3b8;">{{ $users->total() }} user</span>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    <div class="sidebar-avatar" style="width:32px; height:32px; font-size:0.75rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span style="font-weight:600; color:#0f172a;">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td style="color:#64748b;">{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleColors = [
                                        'super_admin' => '#7c3aed',
                                        'ppic' => '#2563eb',
                                        'operator' => '#059669',
                                        'qc' => '#d97706',
                                        'manager' => '#dc2626',
                                    ];
                                    $roleColor = $roleColors[$user->role] ?? '#6b7280';
                                @endphp
                                <span style="background:{{ $roleColor }}; color:white; padding:0.25rem 0.75rem; border-radius:9999px; font-size:0.7rem; font-weight:600;">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td style="color:#64748b; font-size:0.8rem;">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <div class="action-links">
                                    <a href="{{ route('users.edit', $user) }}" class="action-link action-link-edit">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline"
                                              onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-link-delete">Hapus</button>
                                        </form>
                                    @else
                                        <span style="font-size:0.7rem; color:#94a3b8;">(Anda)</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                                    </div>
                                    <div class="empty-state-title">Belum ada user</div>
                                    <div class="empty-state-text">Tambah user pertama</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-footer" style="display:flex; justify-content:center; padding:1rem;">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
