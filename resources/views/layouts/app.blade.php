<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Manajemen Zoom Meeting' }}</title>
    <!-- Tailwind CSS CDN & FontAwesome & Google Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        zoom: {
                            50: '#eef6ff',
                            100: '#d9ebff',
                            500: '#0b5cff',
                            600: '#0047eb',
                            700: '#0037c2',
                        }
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-64 bg-slate-900 text-slate-300 flex flex-col justify-between shrink-0 shadow-xl">
            <div>
                <!-- Brand Header -->
                <div class="p-5 flex items-center gap-3 border-b border-slate-800 bg-slate-950/40">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-video text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-white font-bold tracking-tight leading-none text-base">Manage Zoom</h1>
                        <span class="text-xs text-blue-400 font-medium">Internal Portal</span>
                    </div>
                </div>

                <!-- Nav links -->
                <nav class="p-4 space-y-1.5 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('bookings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('bookings.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-calendar-check w-5 text-center"></i>
                        <span>Peminjaman Zoom</span>
                    </a>

                    <a href="{{ route('calendar.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('calendar.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                        <span>Kalender Rapat</span>
                    </a>

                    <a href="{{ route('memos.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('memos.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-file-signature w-5 text-center"></i>
                        <span>Memo & Persetujuan</span>
                    </a>

                    @if(auth()->user() && auth()->user()->isAdmin())
                        <div class="pt-4 pb-2 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            Administrator
                        </div>

                        <a href="{{ route('zoom-accounts.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('zoom-accounts.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-server w-5 text-center"></i>
                            <span>Master Akun Zoom</span>
                        </a>

                        <a href="{{ route('activity-logs.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('activity-logs.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i>
                            <span>Audit & Log Aktivitas</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Profile & Logout Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white text-xs">
                            {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-semibold text-white truncate max-w-[110px]">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                            <span class="text-[10px] px-1.5 py-0.5 rounded {{ auth()->user() && auth()->user()->isAdmin() ? 'bg-blue-900/60 text-blue-300' : 'bg-slate-800 text-slate-400' }}">
                                {{ auth()->user() && auth()->user()->isAdmin() ? 'Admin' : 'Pegawai' }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-red-400 transition rounded-lg hover:bg-slate-800">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Body -->
        <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
            <!-- Topbar Header -->
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-800">{{ $header ?? 'Dashboard' }}</h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">
                        <i class="fa-regular fa-clock mr-1"></i> {{ now()->format('d M Y, H:i') }} WIB
                    </span>
                    <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3.5 py-2 rounded-lg shadow-sm shadow-blue-500/20 transition">
                        <i class="fa-solid fa-plus"></i> Booking Baru
                    </a>
                </div>
            </header>

            <!-- Notification Alerts -->
            <div class="p-6 pb-0">
                @if (session('success'))
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-lg shadow-sm mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-lg shadow-sm mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-exclamation text-red-500 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="p-6 flex-1">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 px-6 py-3 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Sistem Manajemen Zoom & Booking Portal &bull; Migrated from CI3 to Laravel
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
