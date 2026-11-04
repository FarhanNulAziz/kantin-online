<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kantin Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-jakarta bg-[#F0FDF4] text-slate-800 antialiased min-h-screen flex flex-col selection:bg-emerald-200">

    <header class="bg-white/90 backdrop-blur-md border-b border-emerald-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">

            <a href="{{ route('catalog') }}" class="flex items-center gap-3 shrink-0">
                <div
                    class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white shadow-md shadow-emerald-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M12 2a9 9 0 0 1 9 9v1a8 8 0 0 1-8 8H11a8 8 0 0 1-8-8v-1a9 9 0 0 1 9-9z" />
                        <path d="M7 12a5 5 0 0 0 10 0" />
                        <path d="M12 2v4" />
                    </svg>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-forest-900">Kantin Online</span>
            </a>

            {{-- Pencarian --}}
            <form method="GET" action="{{ route('catalog') }}" class="flex-1 max-w-md hidden md:block">
                @if (request('kategori')) <input type="hidden" name="kategori" value="{{ request('kategori') }}"> @endif
                @if (request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari menu..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-full text-sm placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                </div>
            </form>

            <div class="flex items-center gap-2 sm:gap-3">
                @php $cartCount = \App\Services\Cart::count(); @endphp
                <a href="{{ route('cart.index') }}"
                    class="relative flex items-center gap-2.5 px-3.5 py-2 rounded-full bg-white hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 text-slate-700 transition group">
                    <div class="relative">
                        <svg class="w-5 h-5 text-slate-600 group-hover:text-emerald-600" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        @if ($cartCount > 0)
                            <span
                                class="absolute -top-1.5 -right-2 bg-amber-400 text-forest-900 font-extrabold text-[10px] min-w-4 h-4 px-1 rounded-full flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </div>
                    <span class="text-xs sm:text-sm font-semibold">Keranjang</span>
                </a>

                <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.menus.index') }}"
                            class="text-xs sm:text-sm font-semibold text-slate-600 hover:text-emerald-600 px-2 py-2">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="text-xs sm:text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-full transition">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="text-xs sm:text-sm font-semibold text-slate-600 hover:text-emerald-600 px-3 py-2">Masuk</a>
                    <a href="{{ route('register') }}"
                        class="text-xs sm:text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 px-4 sm:px-5 py-2 rounded-full shadow-sm transition">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        @if (session('success'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-100 text-emerald-900 text-sm font-medium">{{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-800 text-sm font-medium">{{ session('error') }}</div>
        @endif
        @error('qty')
            <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-800 text-sm font-medium">{{ $message }}</div>
        @enderror

        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-emerald-100 mt-12 py-8">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-center gap-3 text-xs text-slate-400">
            <p style="text-align: center">© {{ date('Y') }} Kantin Online</p>
        </div>
    </footer>

</body>

</html>
