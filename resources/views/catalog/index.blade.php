<x-shop-layout>
    @php
        $kategori = request('kategori');
        $sort = request('sort');
        $q = request('q');

        $url = fn($k) => route('catalog', array_filter(['kategori' => $k, 'q' => $q, 'sort' => $sort]));

        $pill = 'font-semibold text-xs sm:text-sm px-5 py-2.5 rounded-full transition';
        $on = 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-700';
        $off = 'bg-white border border-slate-200 text-slate-700 hover:text-emerald-700 hover:border-emerald-300';
    @endphp

    {{-- Hero --}}
    <section
        class="bg-gradient-to-br from-emerald-100/70 via-emerald-50 to-teal-50 rounded-3xl p-6 sm:p-8 md:p-10 mb-8 border border-emerald-200/60 shadow-sm relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-300/20 rounded-full blur-2xl pointer-events-none">
        </div>
        <div class="absolute right-32 -bottom-20 w-48 h-48 bg-amber-300/20 rounded-full blur-xl pointer-events-none">
        </div>

        <div class="relative z-10 max-w-2xl">
            @if ($isFull)
                <div
                    class="inline-flex items-center gap-2 bg-amber-50 border border-amber-300 px-3 py-1 rounded-full text-xs font-semibold text-amber-900 mb-4">
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                    Kantin Penuh: pesanan hanya bisa dibungkus / dijemput
                </div>
            @else
                <div
                    class="inline-flex items-center gap-2 bg-white/80 border border-emerald-200 px-3 py-1 rounded-full text-xs font-semibold text-forest-900 mb-4">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    Kantin Tidak Penuh: bisa makan di tempat atau bungkus
                </div>
            @endif

            <h1
                class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-forest-900 tracking-tight leading-tight mb-3">
                Lapar? Pesan dari kursi kamu, ambil tanpa antre.
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-6">
                Pilih menu favoritmu, bayar di tempat untuk pesanan kecil atau lewat QRIS dan e-wallet, lalu tinggal
                ambil pesananmu.
            </p>

            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-forest-900">
                <span
                    class="flex items-center gap-1.5 bg-white/70 px-3 py-1.5 rounded-full border border-emerald-200/60">⚡
                    Pesan Cepat</span>
                <span
                    class="flex items-center gap-1.5 bg-white/70 px-3 py-1.5 rounded-full border border-emerald-200/60">💵
                    COD sampai Rp20.000</span>
                <span
                    class="flex items-center gap-1.5 bg-white/70 px-3 py-1.5 rounded-full border border-emerald-200/60">💳
                    QRIS &amp; E-Wallet</span>
            </div>
        </div>
    </section>

    {{-- Filter & urutkan --}}
    <section class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
            <a href="{{ $url(null) }}" class="{{ $pill }} {{ !$kategori ? $on : $off }}">Semua</a>
            <a href="{{ $url('makanan') }}" class="{{ $pill }} {{ $kategori === 'makanan' ? $on : $off }}">Makanan</a>
            <a href="{{ $url('minuman') }}" class="{{ $pill }} {{ $kategori === 'minuman' ? $on : $off }}">Minuman</a>
        </div>

        <form method="GET" action="{{ route('catalog') }}"
            class="flex items-center gap-2 self-end sm:self-auto text-xs sm:text-sm">
            @if ($kategori) <input type="hidden" name="kategori" value="{{ $kategori }}"> @endif
            @if ($q) <input type="hidden" name="q" value="{{ $q }}"> @endif
            <label for="sort" class="text-slate-500 font-medium">Urutkan:</label>
            <select id="sort" name="sort" onchange="this.form.submit()"
                class="bg-white border border-slate-200 rounded-full py-1.5 pl-3 pr-8 text-xs sm:text-sm font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer">
                <option value="" @selected(!$sort)>Nama A-Z</option>
                <option value="termurah" @selected($sort === 'termurah')>Harga: Terendah</option>
                <option value="termahal" @selected($sort === 'termahal')>Harga: Tertinggi</option>
            </select>
        </form>
    </section>

    @if ($q)
        <p class="mb-4 text-sm text-slate-500">
            Hasil pencarian "<span class="font-semibold text-slate-700">{{ $q }}</span>"
            <a href="{{ $url($kategori) }}" class="ms-2 text-emerald-700 underline"
                onclick="event.preventDefault(); window.location='{{ route('catalog', array_filter(['kategori' => $kategori, 'sort' => $sort])) }}'">Hapus</a>
        </p>
    @endif

    {{-- Grid menu --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($menus as $menu)
            <article
                class="food-card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col justify-between transition duration-200 hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-500/10">
                <div>
                    <div class="relative h-44 sm:h-48 w-full overflow-hidden bg-slate-100">
                        @if ($menu->gambar_url)
                            <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}" loading="lazy"
                                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl font-extrabold text-slate-300">
                                {{ strtoupper(substr($menu->nama, 0, 1)) }}
                            </div>
                        @endif

                        <span class="absolute top-3 left-3 text-xs font-semibold px-2.5 py-0.5 rounded-full border
                                {{ $menu->kategori === 'makanan'
            ? 'bg-amber-100 text-amber-900 border-amber-200'
            : 'bg-emerald-100 text-emerald-900 border-emerald-200' }}">
                            {{ ucfirst($menu->kategori) }}
                        </span>
                    </div>

                    <div class="p-4 sm:p-5 pb-2">
                        <div class="flex items-baseline justify-between gap-2 mb-1">
                            <h3 class="font-bold text-base sm:text-lg text-slate-800 leading-snug">{{ $menu->nama }}</h3>
                            <span class="font-extrabold text-forest-900 text-base sm:text-lg whitespace-nowrap">
                                Rp{{ number_format($menu->harga, 0, ',', '.') }}
                            </span>
                        </div>
                        @if ($menu->stok > 0 && $menu->stok <= 5)
                            <p class="text-xs text-orange-600 font-medium mb-2">Sisa {{ $menu->stok }}</p>
                        @endif
                    </div>
                </div>

                <div class="p-4 sm:p-5 pt-2">
                    @if ($menu->stok > 0)
                        <form method="POST" action="{{ route('cart.add', $menu) }}" data-qty-form>
                            @csrf
                            <input type="hidden" name="qty" value="1" max="{{ min($menu->stok, 20) }}">

                            <div
                                class="flex items-center border border-slate-200 rounded-full p-1 bg-slate-50 w-28 justify-between mb-3">
                                <button type="button" data-step="-1" aria-label="Kurangi"
                                    class="w-6 h-6 rounded-full bg-white text-slate-600 hover:bg-emerald-100 hover:text-emerald-700 flex items-center justify-center font-bold text-sm shadow-sm transition">−</button>
                                <span data-qty-label class="text-xs font-bold text-slate-700 px-1">1</span>
                                <button type="button" data-step="1" aria-label="Tambah"
                                    class="w-6 h-6 rounded-full bg-white text-slate-600 hover:bg-emerald-100 hover:text-emerald-700 flex items-center justify-center font-bold text-sm shadow-sm transition">+</button>
                            </div>

                            <button
                                class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-xs sm:text-sm py-2.5 rounded-full transition shadow-sm flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                Keranjang
                            </button>
                        </form>
                    @else
                        <span
                            class="block text-center py-2.5 rounded-full bg-slate-100 text-slate-400 text-xs sm:text-sm font-bold">Habis</span>
                    @endif
                </div>
            </article>
        @empty
            <p class="col-span-full text-center text-slate-500 py-16">Menu tidak ditemukan.</p>
        @endforelse
    </section>

    {{-- Cara pesan --}}
    <section class="mt-14 bg-white rounded-3xl p-6 sm:p-8 border border-emerald-100 shadow-sm">
        <div class="text-center max-w-xl mx-auto mb-8">
            <h2 class="text-lg sm:text-xl font-extrabold text-forest-900">Cara Mudah Memesan</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Tanpa desak-desakan, pesanan siap saat kamu datang.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            @foreach ([
                    ['1', 'Pilih Menu', 'Pilih makanan dan minuman favoritmu langsung dari HP atau laptop'],
                    ['2', 'Checkout & Bayar', 'Total sampai Rp20.000 boleh bayar di tempat. Di atasnya bayar lewat QRIS, e-wallet, atau transfer bank.'],
                    ['3', 'Ambil & Nikmati', 'Pantau status pesanan, lalu ambil atau makan di tempat saat sudah siap.'],
                ] as [$no, $judul, $isi])
                <div class="flex flex-col items-center">
                    <div
                        class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 font-extrabold flex items-center justify-center text-lg mb-3">
                        {{ $no }}</div>
                    <h4 class="font-bold text-sm text-slate-800 mb-1">{{ $judul }}</h4>
                    <p class="text-xs text-slate-500 max-w-xs">{{ $isi }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <script>
        document.querySelectorAll('[data-qty-form]').forEach(form => {
            const input = form.querySelector('input[name="qty"]');
            const label = form.querySelector('[data-qty-label]');
            const max = parseInt(input.max, 10) || 20;

            form.querySelectorAll('[data-step]').forEach(btn => {
                btn.addEventListener('click', () => {
                    let v = parseInt(input.value, 10) + parseInt(btn.dataset.step, 10);
                    v = Math.min(max, Math.max(1, v));
                    input.value = v;
                    label.textContent = v;
                });
            });
        });
    </script>
</x-shop-layout>
