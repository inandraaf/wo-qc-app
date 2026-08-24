<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ isset($pageTitle) ? $pageTitle . ' — ' : '' }}{{ config('app.name', 'WO & QC System') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    </head>
    <body>
        <div class="sidebar-layout">
            {{-- Sidebar --}}
            <aside class="sidebar">
                <div class="sidebar-logo">
                    <div class="sidebar-logo-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                            <path d="M9 12h6M9 16h4"/>
                        </svg>
                    </div>
                    <div class="sidebar-logo-text">
                        <span class="sidebar-logo-title">WO & QC</span>
                        <span class="sidebar-logo-sub">Manufacturing</span>
                    </div>
                </div>

                <div class="sidebar-section-label">Menu</div>

                <nav class="sidebar-nav">
                    <a href="{{ route('dashboard') }}"
                       class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="sidebar-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        Dashboard
                    </a>

                    @if(in_array(auth()->user()->role, ['ppic', 'super_admin']))
                    <a href="{{ route('work-orders.index') }}"
                       class="sidebar-item {{ request()->routeIs('work-orders.index', 'work-orders.create', 'work-orders.show', 'work-orders.edit', 'work-orders.update') ? 'active' : '' }}">
                        <svg class="sidebar-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="12" y1="18" x2="12" y2="12"/>
                            <line x1="9" y1="15" x2="15" y2="15"/>
                        </svg>
                        Work Orders
                    </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['operator', 'super_admin']))
                    <a href="{{ route('productions.index') }}"
                       class="sidebar-item {{ request()->routeIs('productions.index', 'productions.store') ? 'active' : '' }}">
                        <svg class="sidebar-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                        Productions
                    </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['qc', 'super_admin']))
                    <a href="{{ route('quality-controls.index') }}"
                       class="sidebar-item {{ request()->routeIs('quality-controls.index', 'quality-controls.store') ? 'active' : '' }}">
                        <svg class="sidebar-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        Quality Control
                    </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['super_admin']))
                    <a href="{{ route('users.index') }}"
                       class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <svg class="sidebar-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                            <path d="M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                        User Management
                    </a>
                    @endif
                </nav>

                <div class="sidebar-footer">
                    <div class="sidebar-user">
                        <div class="sidebar-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="sidebar-user-info">
                            <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                            <div class="sidebar-user-role" style="text-transform:uppercase; font-size:0.65rem;">
                                @switch(auth()->user()->role)
                                    @case('super_admin') Super Admin @break
                                    @case('ppic') PPIC @break
                                    @case('operator') Operator @break
                                    @case('qc') QC @break
                                    @case('manager') Manager @break
                                    @default {{ auth()->user()->role }}
                                @endswitch
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 0.25rem; display:flex; flex-direction:column; gap: 2px;">
                        <a href="{{ route('profile.edit') }}"
                           class="sidebar-item" style="font-size: 0.75rem; padding: 0.4rem 0.75rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>
                            </svg>
                            Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="sidebar-item"
                                    style="width: 100%; background: none; border: none; cursor: pointer; font-size: 0.75rem; padding: 0.4rem 0.75rem; text-align: left;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Main Area --}}
            <div class="main-area">
                <header class="topbar">
                    <div>
                        <div class="topbar-title">@yield('topbar-title', 'Dashboard')</div>
                        <div class="topbar-subtitle">@yield('topbar-subtitle', '')</div>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <div style="text-align:right;">
                            <div style="font-size:0.8rem; font-weight:600; color:#334155;">{{ now()->format('l, d M Y') }}</div>
                            <div style="font-size:0.7rem; color:#94a3b8;">{{ now()->format('H:i') }} WIB</div>
                        </div>
                        <div style="width:1px; height:28px; background:#e2e8f0; margin: 0 0.5rem;"></div>
                        <div class="sidebar-avatar" style="width:36px; height:36px; font-size:0.8rem;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </header>

                <main class="page-content">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
    @stack('scripts')
</html>
