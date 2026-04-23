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
                        primary: { DEFAULT:'#3b82f6', light:'#60a5fa', dark:'#2563eb' },
                        accent:  { DEFAULT:'#38bdf8', light:'#7dd3fc', dark:'#0ea5e9' },
                    },
                    fontFamily: {
                        sans: ['"Poppins"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="flex items-center justify-center min-h-screen p-4" style="background: linear-gradient(135deg, #2563eb, #38bdf8); font-family: 'Poppins', sans-serif;">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @yield('form')
        </div>
    </div>

</body>
</html>