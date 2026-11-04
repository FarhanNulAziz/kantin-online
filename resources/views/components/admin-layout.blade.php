@props(['title' => 'Admin · Kantin Online'])

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-jakarta bg-[#F0FDF4] text-slate-800 antialiased min-h-screen flex flex-col selection:bg-emerald-200">

    <header class="bg-white/90 backdrop-blur-md border-b border-emerald-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-20 flex items-center justify-between gap-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 shrink-0">
                    <div
                        class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white shadow-md shadow-emerald-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M12 2a9 9 0 0 1 9 9v1a8 8 0 0 1-8 8H11a8 8 0 0 1-8-8v-1a9 9 0 0 1 9-9z" />
                            <path d="M7 12a5 5 0 0 0 10 0" />
                            <path d="M12 2v4" />
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <span class="block text-lg font-extrabold tracking-tight text-forest-900">Kantin Online</span>
                        <span class="block text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-600">Admin
                            Panel</span>
                    </div>
                </a>

                @php
                    $navLink = 'px-4 py-2 rounded-full text-sm font-semibold transition whitespace-nowrap';
                    $navOn = 'bg-emerald-600 text-white shadow-sm';
                    $navOff = 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50';
                @endphp

                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ $navLink }} {{ request()->routeIs('admin.dashboard') ? $navOn : $navOff }}">Dashboard</a>
                    <a href="{{ route('admin.menus.index') }}"
                        class="{{ $navLink }} {{ request()->routeIs('admin.menus.*') ? $navOn : $navOff }}">Kelola
                        Menu</a>
                </nav>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="hidden lg:flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-extrabold shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-700">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-400">Administrator</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="text-xs sm:text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-full transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            <nav class="md:hidden flex items-center gap-2 pb-3 overflow-x-auto">
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ $navLink }} {{ request()->routeIs('admin.dashboard') ? $navOn : $navOff }}">Dashboard</a>
                <a href="{{ route('admin.menus.index') }}"
                    class="{{ $navLink }} {{ request()->routeIs('admin.menus.*') ? $navOn : $navOff }}">Kelola Menu</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        @if (session('success'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-100 text-emerald-900 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-800 text-sm font-medium">{{ session('error') }}</div>
        @endif
        @error('status')
            <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-800 text-sm font-medium">{{ $message }}</div>
        @enderror

        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-emerald-100 mt-12 py-8">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
            <p>© {{ date('Y') }} Kantin Online. Panel Admin.</p>
            <p>Pembayaran aman via Midtrans</p>
        </div>
    </footer>

</body>

</html>
