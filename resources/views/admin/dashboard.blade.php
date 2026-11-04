<x-admin-layout title="Dashboard Admin · Kantin Online">
    <section
        class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 text-white p-6 sm:p-8 mb-6 shadow-lg shadow-emerald-500/20">
        <div class="absolute -right-16 -top-20 w-72 h-72 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-24 -bottom-24 w-56 h-56 bg-amber-300/20 rounded-full blur-2xl pointer-events-none">
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <span
                    class="inline-flex items-center gap-2 bg-white/15 border border-white/20 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-200"></span>
                    Dashboard Admin
                </span>
                <h1 class="mt-4 text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight">Halo,
                    {{ auth()->user()->name }}
                </h1>
                <p class="mt-2 text-emerald-50/90 text-sm sm:text-base max-w-xl leading-relaxed">
                    Pantau status kantin, stok menu, dan operasional harian dari satu tempat.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.menus.index') }}"
                    class="inline-flex items-center gap-2 bg-white text-emerald-700 hover:bg-emerald-50 font-bold text-sm px-5 py-2.5 rounded-full transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" />
                    </svg>
                    Kelola Menu
                </a>
                <a href="{{ route('catalog') }}"
                    class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/25 text-white font-bold text-sm px-5 py-2.5 rounded-full transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    Lihat Toko
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Menu</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-forest-900">{{ $totalMenus }}</p>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tersedia</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-emerald-700">{{ $availableMenus }}</p>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Stok Menipis</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-amber-600">{{ $lowStockMenus }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Stok Habis</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-red-600">{{ $outOfStockMenus }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M20 12H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">
        <section class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="relative flex h-2.5 w-2.5">
                            @if (!$isFull)
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            @endif
                            <span
                                class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $isFull ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                        </span>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Status Kantin
                            Saat Ini</span>
                    </div>

                    <h2
                        class="text-2xl sm:text-3xl font-extrabold {{ $isFull ? 'text-amber-700' : 'text-emerald-700' }}">
                        {{ $isFull ? 'Penuh' : 'Tidak Penuh' }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1 max-w-lg leading-relaxed">
                        @if ($isFull)
                            Pelanggan hanya bisa memilih bungkus / dijemput. Opsi makan di tempat dinonaktifkan sampai
                            status diubah.
                        @else
                            Pelanggan bisa memilih makan di tempat atau bungkus / dijemput.
                        @endif
                    </p>

                    @if ($status->updated_at)
                        <p class="text-xs text-slate-400 mt-3">
                            Terakhir diperbarui {{ $status->updated_at->diffForHumans() }}
                            @if ($status->updatedBy)
                                oleh <span class="font-medium text-slate-500">{{ $status->updatedBy->name }}</span>
                            @endif
                        </p>
                    @endif
                </div>

                <div
                    class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 {{ $isFull ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }}">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.canteen-status.update') }}"
                class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                @csrf
                @method('PATCH')
                <button type="submit" name="status" value="tidak_penuh" @disabled(!$isFull)
                    class="flex items-center justify-center gap-2 px-4 py-3 rounded-2xl text-sm font-bold border transition
                        {{ !$isFull
    ? 'bg-emerald-50 border-emerald-200 text-emerald-800 cursor-default'
    : 'bg-white border-slate-200 text-slate-600 hover:border-emerald-300 hover:text-emerald-700 hover:bg-emerald-50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    {{ !$isFull ? 'Tidak Penuh' : 'Set Tidak Penuh' }}
                </button>
                <button type="submit" name="status" value="penuh" @disabled($isFull)
                    class="flex items-center justify-center gap-2 px-4 py-3 rounded-2xl text-sm font-bold border transition
                        {{ $isFull
    ? 'bg-amber-50 border-amber-200 text-amber-900 cursor-default'
    : 'bg-white border-slate-200 text-slate-600 hover:border-amber-300 hover:text-amber-700 hover:bg-amber-50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    {{ $isFull ? 'Penuh' : 'Set Penuh' }}
                </button>
            </form>
        </section>

        <section class="bg-white rounded-3xl border border-emerald-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-extrabold text-forest-900 text-sm">Stok Menipis</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Menu dengan stok 5 atau kurang.</p>
                </div>
                <a href="{{ route('admin.menus.index') }}"
                    class="text-xs font-bold text-emerald-700 hover:text-emerald-800 whitespace-nowrap">Lihat semua</a>
            </div>

            <ul class="divide-y divide-slate-100">
                @forelse ($lowStockList as $menu)
                    <li class="flex items-center gap-3 px-5 py-3.5">
                        @if ($menu->gambar_url)
                            <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}"
                                class="w-10 h-10 rounded-xl object-cover border border-slate-100 shrink-0">
                        @else
                            <div
                                class="w-10 h-10 rounded-xl bg-slate-100 text-slate-300 flex items-center justify-center text-sm font-extrabold shrink-0">
                                {{ strtoupper(substr($menu->nama, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $menu->nama }}</p>
                            <p class="text-[11px] text-slate-400 capitalize">{{ $menu->kategori }}</p>
                        </div>
                        <span
                            class="text-[11px] font-extrabold text-amber-700 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-full whitespace-nowrap">
                            Sisa {{ $menu->stok }}
                        </span>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center">
                        <div
                            class="w-12 h-12 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-600">Semua stok aman</p>
                        <p class="text-xs text-slate-400 mt-1">Tidak ada menu yang perlu segera diisi ulang.</p>
                    </li>
                @endforelse
            </ul>
        </section>
    </div>
</x-admin-layout>
