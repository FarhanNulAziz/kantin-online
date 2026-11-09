<x-admin-layout title="Kelola Menu · Kantin Online">
    <section class="mb-7 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-forest-900 tracking-tight">Kelola Menu</h1>
            <p class="text-sm text-slate-500 mt-1">Atur daftar makanan, minuman, harga, stok, dan ketersediaannya.</p>
        </div>
        <a href="{{ route('admin.menus.create') }}"
            class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-sm px-5 py-2.5 rounded-full transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
            Tambah Menu
        </a>
    </section>

    @php
        $aktif = request('kategori');
        $q = request('q');
        $pill = 'font-semibold text-xs sm:text-sm px-5 py-2.5 rounded-full transition whitespace-nowrap';
        $on = 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-700';
        $off = 'bg-white border border-slate-200 text-slate-700 hover:text-emerald-700 hover:border-emerald-300';
    @endphp

    <section class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <a href="{{ route('admin.menus.index') }}" class="{{ $pill }} {{ !$aktif ? $on : $off }}">Semua</a>
            <a href="{{ route('admin.menus.index', ['kategori' => 'makanan']) }}"
                class="{{ $pill }} {{ $aktif === 'makanan' ? $on : $off }}">Makanan</a>
            <a href="{{ route('admin.menus.index', ['kategori' => 'minuman']) }}"
                class="{{ $pill }} {{ $aktif === 'minuman' ? $on : $off }}">Minuman</a>
        </div>

        <form method="GET" action="{{ route('admin.menus.index') }}" class="flex items-center gap-2">
            @if ($aktif)
                <input type="hidden" name="kategori" value="{{ $aktif }}">
            @endif
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari menu..."
                    class="w-full sm:w-64 pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-full text-sm placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
            </div>
            @if ($q)
                <a href="{{ route('admin.menus.index', array_filter(['kategori' => $aktif])) }}"
                    class="text-xs font-semibold text-slate-500 hover:text-emerald-700 whitespace-nowrap">Reset</a>
            @endif
        </form>
    </section>

    <p class="mb-3 text-xs text-slate-500">
        Menampilkan <span class="font-bold text-slate-700">{{ $menus->total() }}</span> menu
        @if ($q)
            untuk pencarian "<span class="font-semibold text-slate-700">{{ $q }}</span>"
        @endif
    </p>

    <section class="bg-white rounded-3xl border border-emerald-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-emerald-50/60 text-left text-[11px] font-bold uppercase tracking-wider text-emerald-900">
                    <tr>
                        <th class="p-4">Foto</th>
                        <th class="p-4">Nama</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4">Stok</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($menus as $menu)
                        <tr class="hover:bg-emerald-50/30 transition">
                            <td class="p-4">
                                @if ($menu->gambar_url)
                                    <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}"
                                        class="h-14 w-14 object-cover rounded-2xl border border-slate-100">
                                @else
                                    <div
                                        class="h-14 w-14 rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center text-xl font-extrabold">
                                        {{ strtoupper(substr($menu->nama, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="p-4">
                                <p class="font-bold text-slate-800">{{ $menu->nama }}</p>
                            </td>
                            <td class="p-4">
                                <span
                                    class="text-[10px] font-semibold px-2.5 py-1 rounded-full border
                                        {{ $menu->kategori === 'makanan'
                                            ? 'bg-amber-100 text-amber-900 border-amber-200'
                                            : 'bg-emerald-100 text-emerald-900 border-emerald-200' }}">
                                    {{ ucfirst($menu->kategori) }}
                                </span>
                            </td>
                            <td class="p-4 font-extrabold text-forest-900 whitespace-nowrap">
                                Rp{{ number_format($menu->harga, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                <span
                                    class="font-semibold {{ $menu->stok > 0 ? 'text-slate-700' : 'text-red-600' }}">
                                    {{ $menu->stok }}
                                </span>
                            </td>
                            <td class="p-4">
                                <form method="POST" action="{{ route('admin.menus.toggle', $menu) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        class="px-3 py-1.5 rounded-full text-[11px] font-bold transition
                                            {{ $menu->is_available
                                                ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200'
                                                : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                        {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.menus.edit', $menu) }}"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold hover:bg-emerald-100 transition">
                                        Ubah
                                    </a>

                                    <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}"
                                        onsubmit="return confirm('Hapus menu {{ $menu->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded-full bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center">
                                <div
                                    class="w-16 h-16 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="1.8" />
                                    </svg>
                                </div>
                                <p class="font-bold text-slate-700">Menu tidak ditemukan</p>
                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $q || $aktif ? 'Coba ubah kata kunci atau filter kategori.' : 'Belum ada menu yang ditambahkan.' }}
                                </p>
                                <div class="flex flex-wrap items-center justify-center gap-3 mt-5">
                                    @if ($q || $aktif)
                                        <a href="{{ route('admin.menus.index') }}"
                                            class="inline-flex items-center px-4 py-2 rounded-full bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition">Reset
                                            Filter</a>
                                    @endif
                                    <a href="{{ route('admin.menus.create') }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" />
                                        </svg>
                                        Tambah Menu
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5">{{ $menus->links() }}</div>
</x-admin-layout>
