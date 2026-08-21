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
    </head>
    <body style="background: #0f172a; font-family: 'Figtree', sans-serif;">
        <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div style="width: 100%; max-width: 420px;">
                {{-- Logo --}}
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 16px; margin-bottom: 1rem;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                            <path d="M9 12h6M9 16h4"/>
                        </svg>
                    </div>
                    <h1 style="font-size: 1.25rem; font-weight: 800; color: #f1f5f9; letter-spacing: -0.02em;">WO & QC System</h1>
                    <p style="font-size: 0.8rem; color: #475569; margin-top: 0.25rem;">Manufacturing Management</p>
                </div>

                {{-- Card --}}
                <div style="background: #1e293b; border-radius: 1rem; border: 1px solid #334155; padding: 2rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);">
                    {{ $slot }}
                </div>

                <p style="text-align: center; font-size: 0.75rem; color: #334155; margin-top: 1.5rem;">
                    &copy; {{ date('Y') }} WO & QC Manufacturing System
                </p>
            </div>
        </div>
    </body>
</html>
