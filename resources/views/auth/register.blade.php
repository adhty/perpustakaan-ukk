@extends('layouts.auth')
@section('title', 'Daftar Akun')

@section('form')
<div class="p-0">
    {{-- Header --}}
    <div class="text-center mb-5">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-sky-300 rounded-xl flex items-center justify-center mx-auto mb-3 shadow">
            <i class="fa-solid fa-user-plus text-white text-xl"></i>
        </div>
        <h2 class="text-gray-800 text-xl font-bold">Daftar Akun</h2>
        <p class="text-gray-500 text-xs mt-1">Isi data diri dengan lengkap</p>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-3 mb-4">
        <p class="text-red-600 text-xs font-semibold mb-1">Terjadi kesalahan:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)
                <li class="text-red-500 text-[11px]">{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('register.post') }}" method="POST" class="space-y-3">
        @csrf
        
        <div class="grid grid-cols-2 gap-3">
            <div class="col-span-2">
                <label class="text-gray-600 text-xs font-semibold block mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                       placeholder="Nama lengkap" required>
            </div>
            
            <div>
                <label class="text-gray-600 text-xs font-semibold block mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                       placeholder="Username" required>
            </div>
            
            <div>
                <label class="text-gray-600 text-xs font-semibold block mb-1">NIS</label>
                <input type="text" name="nis" value="{{ old('nis') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                       placeholder="Nomor Induk Siswa" required>
            </div>
            
            <div>
                <label class="text-gray-600 text-xs font-semibold block mb-1">Kelas</label>
                <input type="text" name="kelas" value="{{ old('kelas') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                       placeholder="Contoh: XII RPL 1" required>
            </div>
            
            <div>
                <label class="text-gray-600 text-xs font-semibold block mb-1">No. HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                       placeholder="08xxxxxxxxxx">
            </div>
            
            <div class="col-span-2">
                <label class="text-gray-600 text-xs font-semibold block mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                       placeholder="email@sekolah.sch.id" required>
            </div>
            
            <div>
                <label class="text-gray-600 text-xs font-semibold block mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                       placeholder="Min. 6 karakter" required>
            </div>
            
            <div>
                <label class="text-gray-600 text-xs font-semibold block mb-1">Konfirmasi</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:outline-none"
                       placeholder="Ulangi password" required>
            </div>
        </div>

        <button type="submit"
                class="w-full py-2 bg-gradient-to-r from-blue-500 to-sky-400 text-white rounded-lg text-sm font-semibold hover:shadow transition mt-2">
            <i class="fa-solid fa-user-plus mr-1"></i> Daftar
        </button>
    </form>

    <div class="mt-4 text-center">
        <p class="text-gray-500 text-xs">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-blue-500 font-semibold hover:underline">Masuk</a>
        </p>
    </div>
    
    <div class="mt-4 pt-3 border-t text-center">
        <p class="text-gray-400 text-[9px]">Data Anda akan kami jaga kerahasiaannya</p>
    </div>
</div>
@endsection