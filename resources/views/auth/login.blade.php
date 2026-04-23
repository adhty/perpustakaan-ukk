@extends('layouts.auth')
@section('title', 'Masuk')

@section('form')
<div class="p-0">
    {{-- Header --}}
    <div class="text-center mb-5">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-sky-300 rounded-xl flex items-center justify-center mx-auto mb-3 shadow">
            <i class="fa-solid fa-book-open text-white text-xl"></i>
        </div>
        <h2 class="text-gray-800 text-xl font-bold">Selamat Datang!</h2>
        <p class="text-gray-500 text-xs mt-1">Silakan masuk ke akun Anda</p>
    </div>

    {{-- Error Alert --}}
    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-3 mb-4">
        <p class="text-red-600 text-xs">{{ $errors->first() }}</p>
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('login.post') }}" method="POST" class="space-y-3">
        @csrf
        
        <div>
            <label class="text-gray-600 text-xs font-semibold block mb-1">Username / Email</label>
            <input type="text" name="username" value="{{ old('username') }}"
                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                   placeholder="admin@perpustakaan.sch.id" required autofocus>
        </div>

        <div>
            <label class="text-gray-600 text-xs font-semibold block mb-1">Password</label>
            <div class="relative">
                <input type="password" name="password" id="password"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none pr-9"
                       placeholder="********" required>
                <button type="button" onclick="togglePwd()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500">
                    <i class="fa-solid fa-eye text-sm" id="eye-icon"></i>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-gray-300">
                <span class="text-gray-600 text-xs">Ingat saya</span>
            </label>
            <a href="{{ route('forgot') }}" class="text-blue-500 text-xs hover:underline">Lupa Password?</a>
        </div>

        <button type="submit"
                class="w-full py-2 bg-gradient-to-r from-blue-500 to-sky-400 text-white rounded-lg text-sm font-semibold hover:shadow transition">
            <i class="fa-solid fa-arrow-right-to-bracket mr-1"></i> Masuk
        </button>
    </form>

    <div class="mt-4 text-center">
        <p class="text-gray-500 text-xs">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-blue-500 font-semibold hover:underline">Daftar</a>
        </p>
    </div>
    
    <div class="mt-4 pt-3 border-t text-center">
        <p class="text-gray-400 text-[9px]">Perpustakaan Digital SMKS TARUNA BHAKTI</p>
    </div>
</div>

<script>
    function togglePwd() {
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
</script>
@endsection