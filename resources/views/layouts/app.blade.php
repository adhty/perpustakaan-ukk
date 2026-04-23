<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan Digital') – SMK TARUNA BHAKTI</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT:'#3b82f6', light:'#60a5fa', dark:'#2563eb' },
                        accent:  { DEFAULT:'#38bdf8', light:'#7dd3fc', dark:'#0ea5e9' },
                    },
                    fontFamily: {
                        sans: ['"Poppins"', 'sans-serif'],
                        display: ['"Poppins"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *{box-sizing:border-box;}
        body{font-family:'Poppins',sans-serif;background:#f0f9ff;}
        [x-cloak] { display: none !important; }

        .sidebar{
            background:linear-gradient(180deg,#1e40af 0%,#3b82f6 50%,#1e40af 100%);
            box-shadow:6px 0 24px rgba(59,130,246,0.15);
            position:relative;overflow:hidden;
        }
        .sidebar::before{
            content:'';position:absolute;inset:0;
            background-image:linear-gradient(rgba(255,255,255,0.04) 1px,transparent 1px),
                             linear-gradient(90deg,rgba(255,255,255,0.04) 1px,transparent 1px);
            background-size:40px 40px;pointer-events:none;
        }
        .sidebar::after{
            content:'';position:absolute;bottom:-80px;left:-80px;
            width:250px;height:250px;
            background:radial-gradient(circle,rgba(56,189,248,0.15) 0%,transparent 70%);
            pointer-events:none;
        }

        .nav-link{
            display:flex;align-items:center;gap:12px;
            padding:10px 14px;border-radius:14px;
            color:#cbd5e1;font-size:13px;font-weight:500;
            transition:all 0.25s ease;position:relative;z-index:1;
            text-decoration:none;margin:2px 8px;
        }
        .nav-link:hover{
            background:linear-gradient(90deg,rgba(59,130,246,0.15),rgba(56,189,248,0.08));
            color:#ffffff;
            transform:translateX(4px);
        }
        .nav-link.active{
            background:linear-gradient(90deg,#3b82f6,#38bdf8);
            color:#ffffff;
            font-weight:600;
            box-shadow:0 4px 12px rgba(59,130,246,0.35);
        }
        .nav-icon{
            width:34px;height:34px;display:flex;align-items:center;justify-content:center;
            border-radius:10px;background:rgba(255,255,255,0.06);
            flex-shrink:0;font-size:14px;transition:all 0.2s;
        }
        .nav-link:hover .nav-icon{background:rgba(255,255,255,0.1);transform:scale(1.05);}
        .nav-link.active .nav-icon{background:rgba(255,255,255,0.12);color:#ffffff;}

        .nav-section{
            font-size:10px;font-weight:700;letter-spacing:0.12em;
            text-transform:uppercase;
            color:#94a3b8;
            padding:0 16px;
            margin:16px 0 8px;
        }
        .nav-divider{
            height:1px;
            background:linear-gradient(90deg,rgba(255,255,255,0),rgba(255,255,255,0.08),rgba(255,255,255,0));
            margin:8px 16px;
        }

        .logo-area{
            padding:20px 16px;
            border-bottom:1px solid rgba(255,255,255,0.08);
            text-align:center;
        }
        .logo-icon{
            width:48px;height:48px;
            background:linear-gradient(135deg,#3b82f6,#38bdf8);
            border-radius:14px;
            display:flex;align-items:center;justify-content:center;
            margin:0 auto 10px auto;
            box-shadow:0 8px 20px rgba(59,130,246,0.35);
        }
        .logo-text{
            font-family:'Poppins',sans-serif;
            font-weight:800;color:#ffffff;font-size:18px;
            letter-spacing:-0.02em;
        }
        .logo-sub{
            color:#93c5fd;font-size:10px;font-weight:500;
            letter-spacing:0.08em;
        }

        .user-card-bottom{
            margin:16px;
            background:rgba(255,255,255,0.08);
            border:1px solid rgba(255,255,255,0.12);
            border-radius:20px;
            padding:14px;
        }
        .user-avatar-bottom{
            width:44px;height:44px;
            background:linear-gradient(135deg,#3b82f6,#38bdf8);
            border-radius:12px;
            display:flex;align-items:center;justify-content:center;
            color:#ffffff;font-size:16px;font-weight:700;
            box-shadow:0 4px 12px rgba(59,130,246,0.3);
        }
        .logout-btn-bottom{
            width:34px;height:34px;
            background:rgba(255,255,255,0.06);
            border:1px solid rgba(255,255,255,0.12);
            border-radius:10px;
            display:flex;align-items:center;justify-content:center;
            color:#94a3b8;cursor:pointer;transition:all 0.2s;
        }
        .logout-btn-bottom:hover{
            background:rgba(239,68,68,0.2);
            color:#ef4444;
            border-color:#ef4444;
            transform:scale(1.05);
        }

        .topbar{
            background:#ffffff;
            border-bottom:1px solid #e2e8f0;
            box-shadow:0 2px 8px rgba(0,0,0,0.02);
        }

        .card{
            background:#ffffff;
            border-radius:20px;
            box-shadow:0 4px 16px rgba(0,0,0,0.04);
            border:1px solid #e2e8f0;
            padding:24px;
        }
        
        .btn-primary{
            display:inline-flex;align-items:center;gap:8px;padding:10px 20px;
            background:#3b82f6;color:#ffffff;border-radius:14px;font-weight:600;font-size:13px;
            transition:all 0.2s;box-shadow:0 2px 8px rgba(59,130,246,0.25);
            text-decoration:none;border:none;cursor:pointer;
        }
        .btn-primary:hover{background:#2563eb;transform:translateY(-2px);box-shadow:0 6px 16px rgba(59,130,246,0.3);}
        
        .btn-accent{
            display:inline-flex;align-items:center;gap:8px;padding:10px 20px;
            background:#38bdf8;color:#ffffff;border-radius:14px;font-weight:600;font-size:13px;
            transition:all 0.2s;text-decoration:none;border:none;cursor:pointer;
        }
        .btn-accent:hover{background:#0ea5e9;transform:translateY(-2px);}
        
        .btn-danger{
            display:inline-flex;align-items:center;gap:8px;padding:8px 16px;
            background:#ef4444;color:#ffffff;border-radius:14px;font-weight:600;font-size:13px;
            transition:all 0.2s;border:none;cursor:pointer;
        }
        .btn-danger:hover{background:#dc2626;}
        
        .btn-ghost{
            display:inline-flex;align-items:center;gap:8px;padding:8px 16px;
            border:1.5px solid #e2e8f0;color:#475569;border-radius:14px;font-weight:500;font-size:13px;
            transition:all 0.2s;background:#ffffff;text-decoration:none;cursor:pointer;
        }
        .btn-ghost:hover{background:#f8fafc;border-color:#cbd5e1;}
        
        .input{
            width:100%;padding:10px 16px;border:1.5px solid #e2e8f0;border-radius:14px;
            background:#ffffff;font-size:13px;transition:all 0.2s;outline:none;
        }
        .input:focus{border-color:#3b82f6;box-shadow:0 0 0 4px rgba(59,130,246,0.1);}
        
        .label{
            display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;
        }
        
        .badge{
            display:inline-flex;align-items:center;padding:3px 10px;border-radius:99px;
            font-size:11px;font-weight:600;
        }

        ::-webkit-scrollbar{width:5px;height:5px;}
        ::-webkit-scrollbar-track{background:#f1f5f9;}
        ::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:99px;}
        ::-webkit-scrollbar-thumb:hover{background:#94a3b8;}

        @keyframes fadeInRight{
            from{opacity:0;transform:translateX(-12px);}
            to{opacity:1;transform:translateX(0);}
        }
        nav .nav-link{
            animation:fadeInRight 0.3s ease forwards;
            opacity:0;
        }
        nav .nav-link:nth-child(1){animation-delay:0.05s;}
        nav .nav-link:nth-child(2){animation-delay:0.10s;}
        nav .nav-link:nth-child(3){animation-delay:0.15s;}
        nav .nav-link:nth-child(4){animation-delay:0.20s;}
        nav .nav-link:nth-child(5){animation-delay:0.25s;}
        nav .nav-link:nth-child(6){animation-delay:0.30s;}
        nav .nav-link:nth-child(7){animation-delay:0.35s;}
        nav .nav-link:nth-child(8){animation-delay:0.40s;}
        nav .nav-link:nth-child(9){animation-delay:0.45s;}
        nav .nav-link:nth-child(10){animation-delay:0.50s;}
        nav .nav-link:nth-child(11){animation-delay:0.55s;}
    </style>
    @stack('styles')
</head>
<body class="h-full">
<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="sidebar w-64 flex-shrink-0 flex flex-col">

        {{-- LOGO AREA - PALING ATAS --}}
        <div class="logo-area">
            <div class="logo-icon mx-auto">
                <i class="fa-solid fa-book-open" style="color:#ffffff;font-size:22px;"></i>
            </div>
            <div class="logo-text">Perpustakaan</div>
            <div class="logo-sub">DIGITAL</div>
            <div style="color:#93c5fd;font-size:9px;margin-top:8px;">SMK TARUNA BHAKTI</div>
        </div>

        {{-- USER CARD - DI BAWAH LOGO --}}
        <div class="user-card-bottom">
            <div class="flex items-center gap-3">
                <div class="user-avatar-bottom">
                    {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p style="color:#ffffff;font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ auth()->user()->name }}
                    </p>
                    <p style="color:#93c5fd;font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;">
                        {{ auth()->user()->role }}@if(auth()->user()->role==='siswa' && auth()->user()->kelas) · {{ auth()->user()->kelas }}@endif
                    </p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn-bottom" title="Keluar">
                        <i class="fa-solid fa-right-from-bracket" style="font-size:12px;"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- NAVIGATION --}}
        <nav class="flex-1 px-2 py-4 overflow-y-auto">
            @if(auth()->user()->role === 'admin')
                <p class="nav-section">MAIN MENU</p>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
                    <span>Dashboard</span>
                </a>

                <div class="nav-divider"></div>
                <p class="nav-section">MANAGEMENT</p>
                <a href="{{ route('admin.buku.index') }}" class="nav-link {{ request()->routeIs('admin.buku.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-book"></i></span>
                    <span>Kelola Buku</span>
                </a>
                <a href="{{ route('admin.anggota.index') }}" class="nav-link {{ request()->routeIs('admin.anggota.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
                    <span>Kelola Anggota</span>
                </a>
                <a href="{{ route('admin.kategori.index') }}" class="nav-link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-tags"></i></span>
                    <span>Kategori</span>
                </a>

                <div class="nav-divider"></div>
                <p class="nav-section">TRANSACTIONS</p>
                <a href="{{ route('admin.transaksi.index') }}" class="nav-link {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-exchange-alt"></i></span>
                    <span>Transaksi</span>
                    @php $terlambatCount = \App\Models\Pinjam::where('status','dipinjam')->where('tgl_kembali_rencana','<',\Carbon\Carbon::today())->count(); @endphp
                    @if($terlambatCount > 0)
                        <span style="margin-left:auto;background:#ef4444;color:#ffffff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:99px;">{{ $terlambatCount }}</span>
                    @endif
                </a>
                
                {{-- MENU APPROVAL PENGEMBALIAN --}}
                @php
                    $pendingReturnCount = \App\Models\Pinjam::where('status', 'dipinjam')
                        ->where('status_pengembalian', 'pending')
                        ->where('keterangan', 'like', 'Request pengembalian%')
                        ->count();
                @endphp
                <a href="{{ route('admin.pengembalian-approval.index') }}" class="nav-link {{ request()->routeIs('admin.pengembalian-approval.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-rotate-left"></i></span>
                    <span>Approval Pengembalian</span>
                    @if($pendingReturnCount > 0)
                        <span style="margin-left:auto;background:#f59e0b;color:#ffffff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:99px;animation:pulse 2s infinite;">
                            {{ $pendingReturnCount }}
                        </span>
                    @endif
                </a>

                {{-- MENU APPROVE DENDA --}}
                @php
                    $pendingDendaCount = \App\Models\Pinjam::where('status_denda', 'menunggu')->count();
                @endphp
                <a href="{{ route('admin.denda.index') }}" class="nav-link {{ request()->routeIs('admin.denda.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-money-bill-wave"></i></span>
                    <span>Approve Denda</span>
                    @if($pendingDendaCount > 0)
                        <span style="margin-left:auto;background:#ef4444;color:#ffffff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:99px;">
                            {{ $pendingDendaCount }}
                        </span>
                    @endif
                </a>

                <div class="nav-divider"></div>
                <p class="nav-section">REPORTS</p>
                <a href="{{ route('admin.laporan') }}" class="nav-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-chart-simple"></i></span>
                    <span>Laporan</span>
                </a>

            @else
                <p class="nav-section">MY MENU</p>
                <a href="{{ route('siswa.dashboard') }}" class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-home"></i></span>
                    <span>Beranda</span>
                </a>

                <div class="nav-divider"></div>
                <p class="nav-section">BORROWING</p>
                <a href="{{ route('siswa.peminjaman.index') }}" class="nav-link {{ request()->routeIs('siswa.peminjaman.index') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-book-bookmark"></i></span>
                    <span>Pinjam Buku</span>
                </a>
                <a href="{{ route('siswa.pengembalian.index') }}" class="nav-link {{ request()->routeIs('siswa.pengembalian.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-rotate-left"></i></span>
                    <span>Kembalikan Buku</span>
                    @php 
                        $pendingReturnSiswa = \App\Models\Pinjam::where('user_id',auth()->id())
                            ->where('status','dipinjam')
                            ->where('status_pengembalian','pending')
                            ->where('keterangan','like','Request pengembalian%')
                            ->count();
                        $activePinjam = \App\Models\Pinjam::where('user_id',auth()->id())
                            ->where('status','dipinjam')
                            ->where('status_pengembalian','pending')
                            ->count();
                    @endphp
                    @if($pendingReturnSiswa > 0)
                        <span style="margin-left:auto;background:#f59e0b;color:#ffffff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:99px;">
                            {{ $pendingReturnSiswa }}
                        </span>
                    @elseif($activePinjam > 0)
                        <span style="margin-left:auto;background:#3b82f6;color:#ffffff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:99px;">
                            {{ $activePinjam }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('siswa.peminjaman.riwayat') }}" class="nav-link {{ request()->routeIs('siswa.peminjaman.riwayat') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-history"></i></span>
                    <span>Riwayat</span>
                </a>
                <a href="{{ route('siswa.denda.index') }}" class="nav-link {{ request()->routeIs('siswa.denda.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-coins"></i></span>
                    <span>Denda</span>
                    @php
                        $dendaBelumLunas = \App\Models\Pinjam::where('user_id',auth()->id())
                            ->where('denda','>',0)
                            ->where('status_denda','belum_lunas')
                            ->sum('denda');
                    @endphp
                    @if($dendaBelumLunas > 0)
                        <span style="margin-left:auto;background:#ef4444;color:#ffffff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:99px;">
                            Rp {{ number_format($dendaBelumLunas,0,',','.') }}
                        </span>
                    @endif
                </a>

                <div class="nav-divider"></div>
                <p class="nav-section">ACCOUNT</p>
                <a href="{{ route('siswa.profil') }}" class="nav-link {{ request()->routeIs('siswa.profil') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-user-cog"></i></span>
                    <span>Profil Saya</span>
                </a>
            @endif
        </nav>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- TOPBAR --}}
        <header class="topbar px-6 py-3 flex items-center justify-between gap-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
            
            {{-- Left Section - Title & Subtitle --}}
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex w-9 h-9 bg-gradient-to-br from-blue-400 to-sky-300 rounded-xl items-center justify-center shadow-sm">
                    <i class="fa-solid fa-book text-white text-sm"></i>
                </div>
                <div>
                    <h1 style="font-family:'Poppins',sans-serif;font-weight:800;color:#0f172a;font-size:20px;letter-spacing:-0.02em;margin:0;">
                        @yield('page-title','Dashboard')
                    </h1>
                    <p style="color:#64748b;font-size:11px;margin-top:2px;">
                        @yield('page-subtitle','Perpustakaan Digital SMK TARUNA BHAKTI')
                    </p>
                </div>
            </div>

            {{-- Center Section - Search Bar (Admin Only) --}}
            @if(auth()->user()->role === 'admin')
            <div class="hidden md:block flex-1 max-w-md mx-4">
                <form action="{{ route('admin.buku.index') }}" method="GET" class="w-full">
                    <div style="position:relative;width:100%;">
                        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;">
                            <i class="fa-solid fa-search"></i>
                        </span>
                        <input type="text" name="search" placeholder="Cari buku, pengarang, atau kode..." value="{{ request('search') }}"
                            style="width:100%;padding:9px 16px 9px 42px;background:#f8fafc;border:1.5px solid #e2e8f0;
                                   border-radius:40px;font-size:12px;outline:none;transition:all 0.2s;"
                            onfocus="this.style.background='#ffffff';this.style.borderColor='#38bdf8';this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1)'"
                            onblur="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                    </div>
                </form>
            </div>
            @endif

            {{-- Right Section --}}
            <div style="display:flex;align-items:center;gap:12px;">
                
                {{-- Quick Action Button for Siswa --}}
                @if(auth()->user()->role === 'siswa')
                    <a href="{{ route('siswa.peminjaman.index') }}"
                       style="width:38px;height:38px;background:linear-gradient(135deg, #3b82f6, #38bdf8);
                              border-radius:12px;display:flex;align-items:center;justify-content:center;text-decoration:none;
                              transition:all 0.2s;box-shadow:0 2px 6px rgba(59,130,246,0.2);">
                        <i class="fa-solid fa-book-open" style="color:#ffffff;font-size:14px;"></i>
                    </a>
                @endif

                {{-- Date Widget --}}
                <div style="background:#f0f9ff;border-radius:12px;padding:6px 14px;text-align:right;border:1px solid #e0f2fe;">
                    <p style="font-size:12px;font-weight:700;color:#1e40af;margin:0;">{{ now()->isoFormat('D MMM Y') }}</p>
                    <p style="font-size:10px;color:#64748b;margin:2px 0 0 0;">{{ now()->isoFormat('dddd') }}</p>
                </div>
            </div>
        </header>

        {{-- FLASH MESSAGES --}}
        <div class="px-6 pt-4 space-y-2">
            @if(session('success'))
                <div style="display:flex;align-items:center;gap:12px;background:#f0fdf4;border:1px solid #bbf7d0;
                            color:#166534;border-radius:16px;padding:12px 20px;font-size:13px;">
                    <i class="fa-solid fa-circle-check" style="color:#22c55e;font-size:16px;"></i>
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" style="margin-left:auto;color:#86efac;background:none;border:none;cursor:pointer;font-size:16px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div style="display:flex;align-items:center;gap:12px;background:#fef2f2;border:1px solid #fecaca;
                            color:#991b1b;border-radius:16px;padding:12px 20px;font-size:13px;">
                    <i class="fa-solid fa-circle-exclamation" style="color:#ef4444;font-size:16px;"></i>
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" style="margin-left:auto;color:#fca5a5;background:none;border:none;cursor:pointer;font-size:16px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            @if(session('warning'))
                <div style="display:flex;align-items:center;gap:12px;background:#fffbeb;border:1px solid #fde68a;
                            color:#92400e;border-radius:16px;padding:12px 20px;font-size:13px;">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b;font-size:16px;"></i>
                    <span>{{ session('warning') }}</span>
                    <button onclick="this.parentElement.remove()" style="margin-left:auto;color:#fcd34d;background:none;border:none;cursor:pointer;font-size:16px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
        </div>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-y-auto px-6 py-6">
            @yield('content')
        </main>
    </div>
</div>

<style>
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.6;
        }
    }
</style>

@stack('scripts')
</body>
</html>