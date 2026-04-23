<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') – Perpustakaan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT:'#1e3a5f', light:'#2d5282' },
                        accent:  { DEFAULT:'#f59e0b', light:'#fbbf24' },
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .input { @apply w-full px-4 py-3 border border-gray-200 rounded-xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition; }
        .bg-pattern {
            background-image: radial-gradient(circle at 25% 25%, rgba(245,158,11,0.15) 0%, transparent 50%),
                              radial-gradient(circle at 75% 75%, rgba(30,58,95,0.2) 0%, transparent 50%);
        }
    </style>
</head>
<body class="min-h-screen flex" style="background: linear-gradient(135deg, #0f2744 0%, #1e3a5f 50%, #2d5282 100%);">

    {{-- Left Panel --}}
    <div class="hidden lg:flex flex-col justify-center flex-1 px-16 bg-pattern">
        <div class="w-16 h-16 bg-accent rounded-2xl flex items-center justify-center mb-8 shadow-xl">
            <i class="fa-solid fa-book-open text-primary text-3xl"></i>
        </div>
        <h1 class="font-display font-bold text-white text-4xl leading-tight mb-4">
            Perpustakaan<br>Digital Sekolah
        </h1>
        <p class="text-blue-200 text-lg leading-relaxed max-w-sm">
            Sistem manajemen peminjaman buku berbasis web untuk kemudahan seluruh warga sekolah.
        </p>
        <div class="mt-12 space-y-4">
            @foreach(['Kelola data buku dengan mudah', 'Peminjaman & pengembalian digital', 'Laporan transaksi real-time', 'Denda otomatis keterlambatan'] as $f)
                <div class="flex items-center gap-3 text-blue-100">
                    <div class="w-6 h-6 bg-accent/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-check text-accent text-xs"></i>
                    </div>
                    <span class="text-sm">{{ $f }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="w-full lg:w-[480px] flex-shrink-0 flex items-center justify-center p-8">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8">
            @yield('form')
        </div>
    </div>
    
</body>
</html>
