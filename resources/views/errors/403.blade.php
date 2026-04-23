<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 – Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="text-center p-8">
        <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-gray-800 mb-2">403</h1>
        <h2 class="text-xl font-semibold text-gray-600 mb-2">Akses Ditolak</h2>
        <p class="text-gray-500 mb-8">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-900 text-white rounded-xl font-semibold hover:bg-blue-800 transition">
            ← Kembali
        </a>
    </div>
</body>
</html>
