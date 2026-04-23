@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun Anda')

@section('content')
<div class="min-h-screen">
    <div class="max-w-3xl mx-auto">
        
        {{-- Header --}}
        <div class="mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-sky-400 rounded-xl p-4 shadow">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-user-circle text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs">Halaman Profil</p>
                        <p class="text-white font-semibold text-sm">Informasi Akun Anda</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
            
            {{-- Profile Header --}}
            <div class="bg-gradient-to-r from-blue-500 to-sky-400 px-6 py-5">
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <span class="text-white font-bold text-4xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-white text-xl" style="font-family: 'Poppins', sans-serif;">{{ $user->name }}</h3>
                        <p class="text-white/80 text-sm">{{ $user->email }}</p>
                        <div class="flex gap-2 mt-2">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-white/20 text-white">{{ $user->kelas }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-white/20 text-white">NIS: {{ $user->nis }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error Alert --}}
            @if($errors->any())
            <div class="m-5 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                <div class="flex gap-2">
                    <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-red-700 text-sm mb-1" style="font-family: 'Poppins', sans-serif;">Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $e)
                            <li class="text-sm text-red-600" style="font-family: 'Poppins', sans-serif;">{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('siswa.profil.update') }}" method="POST" class="p-6 space-y-5">
                @csrf @method('PUT')
                
                {{-- Informasi Dasar --}}
                <div>
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100 mb-4">
                        <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-address-card text-blue-600 text-xs"></i>
                        </div>
                        <h4 class="font-semibold text-gray-700 text-sm" style="font-family: 'Poppins', sans-serif;">Informasi Dasar</h4>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif;">
                                Nama Lengkap
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200"
                                       style="font-family: 'Poppins', sans-serif;" required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif;">
                                No. HP
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" 
                                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200"
                                       style="font-family: 'Poppins', sans-serif;" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif;">
                                Alamat
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-location-dot absolute left-3 top-3 text-gray-400 text-xs"></i>
                                <textarea name="alamat" rows="2" 
                                          class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200 resize-none"
                                          style="font-family: 'Poppins', sans-serif;" placeholder="Alamat lengkap">{{ old('alamat', $user->alamat) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ganti Password --}}
                <div class="pt-2">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100 mb-4">
                        <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-lock text-blue-600 text-xs"></i>
                        </div>
                        <h4 class="font-semibold text-gray-700 text-sm" style="font-family: 'Poppins', sans-serif;">Ganti Password <span class="text-gray-400 text-xs font-normal">(opsional)</span></h4>
                    </div>
                    
                    <div class="bg-blue-50 rounded-lg p-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif;">
                                    Password Baru
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-key absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="password" name="password" 
                                           class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200"
                                           style="font-family: 'Poppins', sans-serif;" placeholder="Min. 6 karakter">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif;">
                                    Konfirmasi Password
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-check-circle absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="password" name="password_confirmation" 
                                           class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200"
                                           style="font-family: 'Poppins', sans-serif;" placeholder="Ulangi password baru">
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-blue-600 mt-2" style="font-family: 'Poppins', sans-serif;">
                            <i class="fa-regular fa-lightbulb mr-1"></i> Kosongkan jika tidak ingin mengubah password
                        </p>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 text-white text-sm rounded-lg hover:from-blue-700 hover:to-blue-600 transition shadow-md flex items-center gap-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        
        {{-- Footer Info --}}
        <div class="mt-4 text-center">
            <p class="text-xs text-gray-400" style="font-family: 'Poppins', sans-serif;">
                <i class="fa-regular fa-shield mr-1"></i> Data Anda aman dan tidak akan dibagikan
            </p>
        </div>
    </div>
</div>
@endsection