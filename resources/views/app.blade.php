<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan Digital') – SMKN 1</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT:'#1e3a5f', light:'#2d5282', dark:'#152a44' },
                        accent:  { DEFAULT:'#f59e0b', light:'#fbbf24', dark:'#d97706' },
                        surface: '#f8fafc',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Playfair Display"', 'serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f0f4f8; }
        .sidebar { background: linear-gradient(160deg, #1e3a5f 0%, #0f2744 100%); }
        .nav-link { @apply flex items-center gap-3 px-4 py-3 rounded-xl text-blue-100 hover:bg-white/10 hover:text-white transition-all duration-200 text-sm font-medium; }
        .nav-link.active { @apply bg-accent text-primary font-semibold shadow; }
        .card { @apply bg-white rounded-2xl shadow-sm border border-gray-100 p-6; }
        .btn-primary { @apply inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-light transition-all shadow-sm; }
        .btn-accent { @apply inline-flex items-center gap-2 px-5 py-2.5 bg-accent text-primary rounded-xl font-semibold text-sm hover:bg-accent-light transition-all shadow-sm; }
        .btn-danger { @apply inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-xl font-semibold text-sm hover:bg-red-700 transition-all; }
        .btn-ghost { @apply inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-50 transition-all; }
        .input { @apply w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition; }
        .label { @apply block text-sm font-semibold text-gray-700 mb-1.5; }
        .badge { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold; }
        .stat-card { @apply bg-white rounded-2xl p-5 flex items-center gap-4 shadow-sm border border-gray-100; }
    </style>
    @stack('styles')
</head>
<body class="h-full">
<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar w-64 flex-shrink-0 flex flex-col" id="sidebar">
        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-accent rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-book-open text-primary text-lg"></i>
                </div>
                <div>
                    <p class="font-display font-bold text-white text-sm leading-tight">Perpustakaan</p>
                    <p class="text-blue-300 text-xs">Digital SMK TARUNA BHAKTI</p>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
            @if(auth()->user()->role === 'admin')
                <p class="px-4 text-xs font-semibold text-blue-400 uppercase tracking-widest mb-2">Menu Admin</p>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high w-4"></i> Dashboard
                </a>
                <a href="{{ route('admin.buku.index') }}" class="nav-link {{ request()->routeIs('admin.buku.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-book w-4"></i> Kelola Buku
                </a>
                <a href="{{ route('admin.anggota.index') }}" class="nav-link {{ request()->routeIs('admin.anggota.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users w-4"></i> Kelola Anggota
                </a>
                <a href="{{ route('admin.transaksi.index') }}" class="nav-link {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-arrow-right-arrow-left w-4"></i> Transaksi
                </a>
                <a href="{{ route('admin.laporan') }}" class="nav-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-bar w-4"></i> Laporan
                </a>
            @else
                <p class="px-4 text-xs font-semibold text-blue-400 uppercase tracking-widest mb-2">Menu Siswa</p>
                <a href="{{ route('siswa.dashboard') }}" class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house w-4"></i> Dashboard
                </a>
                <a href="{{ route('siswa.peminjaman.index') }}" class="nav-link {{ request()->routeIs('siswa.peminjaman.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-book-bookmark w-4"></i> Pinjam Buku
                </a>
                <a href="{{ route('siswa.pengembalian.index') }}" class="nav-link {{ request()->routeIs('siswa.pengembalian.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-rotate-left w-4"></i> Kembalikan Buku
                </a>
                <a href="{{ route('siswa.peminjaman.riwayat') }}" class="nav-link {{ request()->routeIs('siswa.peminjaman.riwayat') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left w-4"></i> Riwayat
                </a>
                {{-- MENU Denda Saya --}}
                <a href="{{ route('siswa.denda.index') }}" class="nav-link {{ request()->routeIs('siswa.denda.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-money-bill w-4"></i> Denda Saya
                    @php
                        $totalDenda = App\Models\Pinjam::where('user_id', auth()->id())
                            ->where('denda', '>', 0)
                            ->where('status_denda', 'belum_lunas')
                            ->sum('denda');
                    @endphp
                    @if($totalDenda > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ number_format($totalDenda) }}</span>
                    @endif
                </a>
                <a href="{{ route('siswa.profil') }}" class="nav-link {{ request()->routeIs('siswa.profil') ? 'active' : '' }}">
                    <i class="fa-solid fa-user w-4"></i> Profil Saya
                </a>
            @endif
        </nav>

        {{-- User Info --}}
        <div class="px-3 py-4 border-t border-white/10">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-9 h-9 bg-accent/30 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-user text-accent text-sm"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-blue-300 text-xs capitalize">{{ auth()->user()->role }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-blue-300 hover:text-red-400 transition" title="Logout">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="font-display font-bold text-primary text-xl">@yield('page-title', 'Dashboard')</h1>
                <p class="text-gray-400 text-xs mt-0.5">@yield('page-subtitle', 'Perpustakaan Digital SMKN 1')</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-1 text-sm">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-1 text-sm">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto px-6 py-4">
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>