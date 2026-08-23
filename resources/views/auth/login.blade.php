<x-guest-layout>
    {{-- System Info Hero --}}
    <div style="margin-bottom: 1.5rem; text-align: center;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 12px; margin-bottom: 0.75rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <path d="M9 12h6M9 16h4"/>
            </svg>
        </div>
        <h2 style="font-size: 1rem; font-weight: 800; color: #f1f5f9; letter-spacing: -0.01em;">WO & QC System</h2>
        <p style="font-size: 0.75rem; color: #475569; margin-top: 0.25rem;">Manufacturing Management</p>
    </div>

    {{-- Info Cards --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.25rem;">
        <div style="background: #0f172a; border: 1px solid #1e293b; border-radius: 0.5rem; padding: 0.625rem; text-align: center;">
            <div style="font-size: 1rem; font-weight: 800; color: #3b82f6;">01</div>
            <div style="font-size: 0.65rem; color: #475569; margin-top: 0.125rem;">Buat Work Order</div>
        </div>
        <div style="background: #0f172a; border: 1px solid #1e293b; border-radius: 0.5rem; padding: 0.625rem; text-align: center;">
            <div style="font-size: 1rem; font-weight: 800; color: #22c55e;">02</div>
            <div style="font-size: 0.65rem; color: #475569; margin-top: 0.125rem;">Catat Produksi</div>
        </div>
        <div style="background: #0f172a; border: 1px solid #1e293b; border-radius: 0.5rem; padding: 0.625rem; text-align: center;">
            <div style="font-size: 1rem; font-weight: 800; color: #f59e0b;">03</div>
            <div style="font-size: 0.65rem; color: #475569; margin-top: 0.125rem;">Input QC</div>
        </div>
        <div style="background: #0f172a; border: 1px solid #1e293b; border-radius: 0.5rem; padding: 0.625rem; text-align: center;">
            <div style="font-size: 1rem; font-weight: 800; color: #8b5cf6;">04</div>
            <div style="font-size: 0.65rem; color: #475569; margin-top: 0.125rem;">Monitoring Real-time</div>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 1rem;">
        @csrf

        <div style="text-align: center; margin-bottom: 0.25rem;">
            <h3 style="font-size: 0.875rem; font-weight: 700; color: #e2e8f0;">Masuk ke Sistem</h3>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="alert alert-info" style="background:#1e3a8a20; border-color:#3b82f6; color:#93c5fd;">
                {{ session('status') }}
            </div>
        @endif

        {{-- Email --}}
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="email" style="color: #94a3b8;">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="form-control" style="background:#0f172a; border-color:#334155; color:#f1f5f9;"
                   placeholder="email@contoh.com" required autofocus>
            @error('email')
                <div class="form-error" style="color:#f87171;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="password" style="color: #94a3b8;">Password</label>
            <input type="password" id="password" name="password"
                   class="form-control" style="background:#0f172a; border-color:#334155; color:#f1f5f9;"
                   placeholder="••••••••" required autocomplete="current-password">
            @error('password')
                <div class="form-error" style="color:#f87171;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember --}}
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="checkbox" id="remember_me" name="remember" style="accent-color:#2563eb;">
            <label for="remember_me" style="font-size: 0.8rem; color: #64748b;">Ingat saya</label>
        </div>

        {{-- Submit --}}
        <button type="submit" style="
            width: 100%;
            padding: 0.625rem 1rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: inherit;
        " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(37,99,235,0.4)'"
           onmouseout="this.style.transform=''; this.style.boxShadow=''">
            Masuk
        </button>

        @if (Route::has('password.request'))
            <div style="text-align: center;">
                <a href="{{ route('password.request') }}" style="font-size: 0.75rem; color: #475569; text-decoration: none;">
                    Lupa password?
                </a>
            </div>
        @endif

        {{-- Demo accounts --}}
        <div style="margin-top: 0.5rem; padding: 0.75rem; background:#0f172a; border-radius:0.5rem; border:1px solid #1e293b;">
            <p style="font-size:0.65rem; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">Akun Demo</p>
            @foreach(\App\Models\User::select('email','role')->get() as $u)
                <div style="display:flex; justify-content:space-between; font-size:0.7rem; color:#475569; padding:0.125rem 0;">
                    <span>{{ $u->email }}</span>
                    <span style="color:#3b82f6; font-weight:600;">{{ $u->role }}</span>
                </div>
            @endforeach
            <div style="font-size:0.65rem; color:#334155; margin-top:0.375rem;">Password: <span style="color:#475569; font-family:monospace;">password</span></div>
        </div>
    </form>
</x-guest-layout>
