<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manage Zoom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 antialiased text-gray-800">
    <div class="max-w-sm w-full">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="w-10 h-10 bg-gray-900 rounded mx-auto flex items-center justify-center text-white mb-3">
                <i class="fa-solid fa-video text-sm"></i>
            </div>
            <h1 class="text-lg font-bold text-gray-900 tracking-tight">Manage Zoom</h1>
            <p class="text-gray-500 text-xs mt-0.5">Sistem Peminjaman & Manajemen Akun</p>
        </div>

        <!-- Form Card (Flat Minimalist) -->
        <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
            @if ($errors->any())
                <div class="mb-4 p-2.5 bg-red-50 border border-red-200 text-red-700 text-xs rounded flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Username atau Email</label>
                    <input type="text" name="login" value="{{ old('login') }}" required autofocus
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500 transition"
                        placeholder="admin atau staf@institusi.ac.id">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500 transition"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-gray-900 focus:ring-0">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full mt-2 py-2 bg-gray-900 hover:bg-black text-white text-xs font-medium rounded transition">
                    Masuk ke Sistem
                </button>
            </form>

            <div class="mt-5 pt-4 border-t border-gray-100 text-center">
                <span class="text-[11px] text-gray-400">Default login: <span class="text-gray-600 font-mono">admin / password</span></span>
            </div>
        </div>
    </div>
</body>
</html>
