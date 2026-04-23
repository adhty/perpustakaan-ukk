@extends('layouts.auth')
@section('title', 'Reset Password')

@section('form')
<div class="p-0">
    {{-- Header --}}
    <div class="text-center mb-5">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-sky-300 rounded-xl flex items-center justify-center mx-auto mb-3 shadow">
            <i class="fa-solid fa-lock text-white text-xl"></i>
        </div>
        <h2 class="text-gray-800 text-xl font-bold">Reset Password</h2>
        <p class="text-gray-500 text-xs mt-1">Masukkan kode OTP dan password baru</p>
    </div>

    {{-- Error Alert --}}
    @if (session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-3 mb-4">
        <p class="text-red-600 text-xs">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('reset.post') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">

        <div>
            <label class="text-gray-600 text-xs font-semibold block mb-1">Kode OTP</label>
            <input type="text" name="otp"
                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                   placeholder="Masukkan kode OTP" required>
        </div>

        <div>
            <label class="text-gray-600 text-xs font-semibold block mb-1">Password Baru</label>
            <div class="relative">
                <input type="password" name="password" id="password"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none pr-9"
                       placeholder="Minimal 6 karakter" required>
                <button type="button" onclick="togglePassword()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500">
                    <i class="fa-solid fa-eye text-sm" id="eye-icon"></i>
                </button>
            </div>
        </div>

        <div>
            <label class="text-gray-600 text-xs font-semibold block mb-1">Konfirmasi Password</label>
            <div class="relative">
                <input type="password" name="password_confirmation" id="confirm_password"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none pr-9"
                       placeholder="Ulangi password baru" required>
                <button type="button" onclick="toggleConfirmPassword()"
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
        <a href="{{ route('login') }}" class="text-blue-500 text-xs hover:underline">
            Kembali ke Login
        </a>
    </div>
    
    <div class="mt-5 pt-3 border-t text-center">
        <p class="text-gray-400 text-[9px]">Perpustakaan Digital SMKS TARUNA BHAKTI</p>
    </div>
</div>

<script>
    function togglePassword() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
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

    function toggleConfirmPassword() {
        const pwd = document.getElementById('confirm_password');
        const icon = document.getElementById('eye-icon-confirm');
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