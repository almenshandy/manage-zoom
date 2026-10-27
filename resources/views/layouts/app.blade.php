<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Manajemen Zoom Meeting' }}</title>
    <!-- Favicon & Custom App Icon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    <!-- Tailwind CSS CDN & FontAwesome & Inter Font -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Inter"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Flat UI Reset for professional minimalism */
        body { font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11'; }
        .border-subtle { border-color: #e5e7eb; }

        /* Custom Sleek & Thin Scrollbar (WebKit: Chrome, Edge, Safari) */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Firefox Support */
        * {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans text-sm selection:bg-gray-200">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar Navigation (Clean, Flat, Bordered) -->
        <aside class="w-full md:w-64 bg-white border-r border-gray-200 flex flex-col justify-between shrink-0 md:sticky md:top-0 md:h-screen md:overflow-y-auto">
            <div>
                <!-- Brand Header -->
                <div class="h-14 px-5 flex items-center gap-3 border-b border-gray-200 bg-white">
                    <img src="{{ asset('icon.svg') }}" alt="Logo ManageZoom" class="w-7 h-7 rounded shadow-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-gray-900 tracking-tight">ManageZoom</span>
                        <span class="text-[10px] font-medium bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded border border-gray-200">v2.0</span>
                    </div>
                </div>

                <!-- Nav links -->
                <nav class="p-3 space-y-1 text-xs font-medium">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded border {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900 border-gray-300 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent' }} transition-colors">
                        <i class="fa-solid fa-chart-pie w-4 text-center {{ request()->routeIs('dashboard') ? 'text-gray-900' : 'text-gray-400' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('bookings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded border {{ request()->routeIs('bookings.*') ? 'bg-gray-100 text-gray-900 border-gray-300 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent' }} transition-colors">
                        <i class="fa-solid fa-calendar-check w-4 text-center {{ request()->routeIs('bookings.*') ? 'text-gray-900' : 'text-gray-400' }}"></i>
                        <span>Peminjaman Zoom</span>
                    </a>

                    <a href="{{ route('calendar.index') }}" class="flex items-center gap-3 px-3 py-2 rounded border {{ request()->routeIs('calendar.*') ? 'bg-gray-100 text-gray-900 border-gray-300 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent' }} transition-colors">
                        <i class="fa-solid fa-calendar-days w-4 text-center {{ request()->routeIs('calendar.*') ? 'text-gray-900' : 'text-gray-400' }}"></i>
                        <span>Kalender Rapat</span>
                    </a>

                    <a href="{{ route('memos.index') }}" class="flex items-center gap-3 px-3 py-2 rounded border {{ request()->routeIs('memos.*') ? 'bg-gray-100 text-gray-900 border-gray-300 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent' }} transition-colors">
                        <i class="fa-solid fa-file-lines w-4 text-center {{ request()->routeIs('memos.*') ? 'text-gray-900' : 'text-gray-400' }}"></i>
                        <span>Memo & Persetujuan</span>
                    </a>

                    @if(auth()->user() && auth()->user()->isAdmin())
                        <div class="pt-4 pb-1.5 px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400">
                            Administrator
                        </div>

                        <a href="{{ route('zoom-accounts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded border {{ request()->routeIs('zoom-accounts.*') ? 'bg-gray-100 text-gray-900 border-gray-300 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent' }} transition-colors">
                            <i class="fa-solid fa-server w-4 text-center {{ request()->routeIs('zoom-accounts.*') ? 'text-gray-900' : 'text-gray-400' }}"></i>
                            <span>Master Akun Zoom</span>
                        </a>

                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded border {{ request()->routeIs('users.*') ? 'bg-gray-100 text-gray-900 border-gray-300 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent' }} transition-colors">
                            <i class="fa-solid fa-users-gear w-4 text-center {{ request()->routeIs('users.*') ? 'text-gray-900' : 'text-gray-400' }}"></i>
                            <span>Manajemen Pengguna</span>
                        </a>

                        <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-3 py-2 rounded border {{ request()->routeIs('projects.*') ? 'bg-gray-100 text-gray-900 border-gray-300 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent' }} transition-colors">
                            <i class="fa-solid fa-briefcase w-4 text-center {{ request()->routeIs('projects.*') ? 'text-gray-900' : 'text-gray-400' }}"></i>
                            <span>Master Proyek</span>
                        </a>

                        <a href="{{ route('backup.index') }}" class="flex items-center gap-3 px-3 py-2 rounded border {{ request()->routeIs('backup.*') ? 'bg-gray-100 text-gray-900 border-gray-300 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent' }} transition-colors">
                            <i class="fa-solid fa-database w-4 text-center {{ request()->routeIs('backup.*') ? 'text-gray-900' : 'text-gray-400' }}"></i>
                            <span>Backup & Ekspor DB</span>
                        </a>

                        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded border {{ request()->routeIs('settings.*') ? 'bg-gray-100 text-gray-900 border-gray-300 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent' }} transition-colors">
                            <i class="fa-solid fa-sliders w-4 text-center {{ request()->routeIs('settings.*') ? 'text-gray-900' : 'text-gray-400' }}"></i>
                            <span>Pengaturan Zoom</span>
                        </a>

                        <a href="{{ route('activity-logs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded border {{ request()->routeIs('activity-logs.*') ? 'bg-gray-100 text-gray-900 border-gray-300 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent' }} transition-colors">
                            <i class="fa-solid fa-clock-rotate-left w-4 text-center {{ request()->routeIs('activity-logs.*') ? 'text-gray-900' : 'text-gray-400' }}"></i>
                            <span>Audit & Log Aktivitas</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Profile & Logout Footer -->
            <div class="p-3 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded bg-gray-200 border border-gray-300 flex items-center justify-center font-semibold text-gray-700 text-xs">
                            {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                        </div>
                        <div class="overflow-hidden leading-tight">
                            <p class="text-xs font-semibold text-gray-900 truncate max-w-[110px]">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                            <span class="text-[10px] text-gray-500 font-normal">
                                {{ auth()->user() && auth()->user()->isAdmin() ? 'Administrator' : 'Staf Pegawai' }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white rounded border border-transparent hover:border-gray-200 transition">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Body -->
        <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden bg-gray-50/50">
            <!-- Topbar Header (Flat, border-only) -->
            <header class="h-14 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 leading-tight">{{ $header ?? 'Dashboard' }}</h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500 hidden sm:inline border-r border-gray-200 pr-3">
                        <i class="fa-regular fa-clock mr-1 text-gray-400"></i> {{ now()->format('d M Y, H:i') }} WIB
                    </span>
                    <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-1.5 bg-gray-900 hover:bg-black text-white text-xs font-medium px-3 py-1.5 rounded transition">
                        <i class="fa-solid fa-plus text-[10px]"></i> Booking Baru
                    </a>
                </div>
            </header>

            <!-- Toast Container (Flat Clean) -->
            <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
                @if (session('success'))
                    <div class="toast-item pointer-events-auto flex items-start gap-3 p-3.5 bg-white border border-gray-300 rounded shadow-sm text-xs transition-all duration-200 transform translate-y-0 opacity-100">
                        <i class="fa-solid fa-check text-emerald-600 mt-0.5 text-sm"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Sukses</p>
                            <p class="text-gray-600 mt-0.5">{{ session('success') }}</p>
                        </div>
                        <button onclick="dismissToast(this.closest('.toast-item'))" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="toast-item pointer-events-auto flex items-start gap-3 p-3.5 bg-white border border-red-300 rounded shadow-sm text-xs transition-all duration-200 transform translate-y-0 opacity-100">
                        <i class="fa-solid fa-exclamation text-red-600 mt-0.5 text-sm"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-red-900">Perhatian</p>
                            <p class="text-gray-600 mt-0.5">{{ session('error') }}</p>
                        </div>
                        <button onclick="dismissToast(this.closest('.toast-item'))" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="toast-item pointer-events-auto flex items-start gap-3 p-3.5 bg-white border border-gray-300 rounded shadow-sm text-xs transition-all duration-200 transform translate-y-0 opacity-100">
                        <i class="fa-solid fa-info text-blue-600 mt-0.5 text-sm"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Informasi</p>
                            <p class="text-gray-600 mt-0.5">{{ session('info') }}</p>
                        </div>
                        <button onclick="dismissToast(this.closest('.toast-item'))" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="p-6 flex-1">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 px-6 py-2.5 text-center text-[11px] text-gray-500">
                &copy; {{ date('Y') }} Sistem Manajemen Zoom &bull; Versi Flat & Minimalis
            </footer>
        </div>
    </div>

    <script>
        function dismissToast(element) {
            if (!element) return;
            element.classList.add('opacity-0', 'translate-x-2');
            setTimeout(() => {
                element.remove();
            }, 200);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const toasts = document.querySelectorAll('.toast-item');
            toasts.forEach(toast => {
                setTimeout(() => {
                    dismissToast(toast);
                }, 4000);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
