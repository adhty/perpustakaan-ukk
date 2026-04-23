@extends('layouts.auth')
@section('title', 'Lupa Password')

@section('form')
<div class="p-0">
    {{-- Header --}}
    <div class="text-center mb-5">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-sky-300 rounded-xl flex items-center justify-center mx-auto mb-3 shadow">
            <i class="fa-solid fa-key text-white text-xl"></i>
        </div>
        <h2 class="text-gray-800 text-xl font-bold">Lupa Password?</h2>
        <p class="text-gray-500 text-xs mt-1">
            @if (!session('otp_sent'))
                Masukkan email untuk menerima kode OTP
            @else
                Masukkan kode OTP dan password baru
            @endif
        </p>
    </div>

    {{-- STEP 1: INPUT EMAIL --}}
    @if (!session('otp_sent'))
        <form action="{{ route('forgot.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="text-gray-600 text-xs font-semibold block mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                       placeholder="Masukkan email Anda" required>
            </div>

            <button type="submit"
                    class="w-full py-2 bg-gradient-to-r from-blue-500 to-sky-400 text-white rounded-lg text-sm font-semibold hover:shadow transition">
                Kirim OTP
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-blue-500 text-xs hover:underline">
                Kembali ke Login
            </a>
        </div>
    @endif

    {{-- STEP 2: FORM OTP + PASSWORD --}}
    @if (session('otp_sent'))
        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-3 mb-4">
            <p class="text-green-700 text-xs">OTP sudah dikirim ke: <strong>{{ session('email_user') }}</strong></p>
        </div>

        <form action="{{ route('reset.post') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="email" value="{{ session('email_user') }}">

            <div>
                <label class="text-gray-600 text-xs font-semibold block mb-1">Kode OTP</label>
                <input type="text" name="otp"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                       placeholder="Masukkan kode OTP" required>
            </div>

            <div>
                <label class="text-gray-600 text-xs font-semibold block mb-1">Password Baru</label>
                <div class="relative">
                    <input type="password" name="password" id="new_password"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none pr-9"
                           placeholder="Minimal 6 karakter" required>
                    <button type="button" onclick="togglePassword('new_password', 'eye-icon-new')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500">
                        <i class="fa-solid fa-eye text-sm" id="eye-icon-new"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="text-gray-600 text-xs font-semibold block mb-1">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="confirm_password"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none pr-9"
                           placeholder="Ulangi password baru" required>
                    <button type="button" onclick="togglePassword('confirm_password', 'eye-icon-confirm')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500">
                        <i class="fa-solid fa-eye text-sm" id="eye-icon-confirm"></i>
                    </button>
                </div>
            </div>

            <button type="submit"
                    class="w-full py-2 bg-gradient-to-r from-blue-500 to-sky-400 text-white rounded-lg text-sm font-semibold hover:shadow transition mt-2">
                Reset Password
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-gray-500 text-xs">
                Tidak menerima kode?
                <a href="{{ route('forgot') }}" class="text-blue-500 hover:underline">Kirim Ulang OTP</a>
            </p>
        </div>
    @endif
    
    <div class="mt-5 pt-3 border-t text-center">
        <p class="text-gray-400 text-[9px]">Perpustakaan Digital SMKS TARUNA BHAKTI</p>
    </div>
</div>

<script>
    function togglePassword(fieldId, iconId) {
        const pwd = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection