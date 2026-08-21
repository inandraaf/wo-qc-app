<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
        @csrf

        <div style="text-align: center; margin-bottom: 0.5rem;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: #f1f5f9;">Create Account</h2>
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Daftar user baru ke sistem</p>
        </div>

        <!-- Name -->
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="name" style="color: #94a3b8;">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   class="form-control" style="background:#0f172a; border-color:#334155; color:#f1f5f9;"
                   placeholder="Nama Anda" required autofocus autocomplete="name">
            @error('name')
                <div class="form-error" style="color:#f87171;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="email" style="color: #94a3b8;">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="form-control" style="background:#0f172a; border-color:#334155; color:#f1f5f9;"
                   placeholder="user@example.com" required autocomplete="username">
            @error('email')
                <div class="form-error" style="color:#f87171;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="password" style="color: #94a3b8;">Password</label>
            <input type="password" id="password" name="password"
                   class="form-control" style="background:#0f172a; border-color:#334155; color:#f1f5f9;"
                   placeholder="Min. 8 karakter" required autocomplete="new-password">
            @error('password')
                <div class="form-error" style="color:#f87171;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="password_confirmation" style="color: #94a3b8;">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control" style="background:#0f172a; border-color:#334155; color:#f1f5f9;"
                   placeholder="Ulangi password" required autocomplete="new-password">
            @error('password_confirmation')
                <div class="form-error" style="color:#f87171;">{{ $message }}</div>
            @enderror
        </div>

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
            Daftar Akun
        </button>

        <div style="text-align: center;">
            <a href="{{ route('login') }}" style="font-size: 0.8rem; color: #64748b; text-decoration: none;">
                Sudah punya akun? <span style="color:#3b82f6;">Sign in</span>
            </a>
        </div>
    </form>
</x-guest-layout>
