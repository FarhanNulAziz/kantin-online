<x-shop-layout>
    @php
        $jumlahItem = $items->sum('qty');
    @endphp

    <section class="mb-7 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-forest-900 tracking-tight">Keranjang Belanja</h1>
            <p class="text-sm text-slate-500 mt-1">
                @if ($items->isEmpty())
                    Belum ada menu yang dipilih.
                @else
                    {{ $jumlahItem }} item siap dipesan. Periksa kembali sebelum checkout.
                @endif
            </p>
        </div>
        <a href="{{ route('catalog') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
            Lanjut pilih menu
        </a>
    </section>

    @if ($items->isEmpty())
        <section class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-10 sm:p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 mb-5">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="1.8" />
                </svg>
            </div>
            <h2 class="text-lg sm:text-xl font-extrabold text-forest-900 mb-2">Keranjangmu masih kosong</h2>
            <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">Yuk pilih makanan atau minuman favoritmu dulu.
                Pesanan bisa diambil tanpa antre.</p>
            <a href="{{ route('catalog') }}"
                class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-sm px-6 py-3 rounded-full transition shadow-sm">
                Lihat Menu
            </a>
        </section>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_360px] gap-6 items-start">
            <section class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div
                    class="hidden sm:grid grid-cols-[minmax(0,1fr)_140px_120px_40px] gap-4 px-5 py-3 bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wide text-slate-400">
                    <span>Menu</span>
                    <span class="text-center">Jumlah</span>
                    <span class="text-right">Subtotal</span>
                    <span></span>
                </div>

                <ul class="divide-y divide-slate-100">
                    @foreach ($items as $item)
                        @php
                            $menu = $item['menu'];
                            $max = min($menu->stok, 20);
                        @endphp
                        <li class="p-4 sm:p-5">
                            <div
                                class="grid grid-cols-1 sm:grid-cols-[minmax(0,1fr)_140px_120px_40px] gap-4 sm:items-center">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-slate-100 shrink-0">
                                        @if ($menu->gambar_url)
                                            <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full flex items-center justify-center text-2xl font-extrabold text-slate-300">
                                                {{ strtoupper(substr($menu->nama, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-sm sm:text-base text-slate-800 truncate">
                                            {{ $menu->nama }}</h3>
                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            <span
                                                class="text-[10px] font-semibold px-2 py-0.5 rounded-full border
                                                    {{ $menu->kategori === 'makanan'
                                                        ? 'bg-amber-100 text-amber-900 border-amber-200'
                                                        : 'bg-emerald-100 text-emerald-900 border-emerald-200' }}">
                                                {{ ucfirst($menu->kategori) }}
                                            </span>
                                            <span class="text-xs text-slate-500">
                                                Rp{{ number_format($menu->harga, 0, ',', '.') }} / item
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('cart.update', $menu) }}" data-cart-qty
                                    class="justify-self-start sm:justify-self-center">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="qty" value="{{ $item['qty'] }}"
                                        max="{{ $max }}">
                                    <div
                                        class="flex items-center border border-slate-200 rounded-full p-1 bg-slate-50 w-28 justify-between">
                                        <button type="button" data-step="-1" aria-label="Kurangi"
                                            class="w-6 h-6 rounded-full bg-white text-slate-600 hover:bg-emerald-100 hover:text-emerald-700 flex items-center justify-center font-bold text-sm shadow-sm transition">−</button>
                                        <span data-qty-label
                                            class="text-xs font-bold text-slate-700 px-1">{{ $item['qty'] }}</span>
                                        <button type="button" data-step="1" aria-label="Tambah"
                                            class="w-6 h-6 rounded-full bg-white text-slate-600 hover:bg-emerald-100 hover:text-emerald-700 flex items-center justify-center font-bold text-sm shadow-sm transition">+</button>
                                    </div>
                                </form>

                                <div class="flex items-center justify-between sm:block sm:text-right">
                                    <span
                                        class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 sm:hidden">Subtotal</span>
                                    <span class="font-extrabold text-forest-900 text-sm sm:text-base whitespace-nowrap">
                                        Rp{{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </span>
                                </div>

                                <form method="POST" action="{{ route('cart.remove', $menu) }}"
                                    class="justify-self-end" x-data="{ confirming: false }">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click="confirming = true"
                                        aria-label="Hapus {{ $menu->nama }}" title="Hapus"
                                        class="w-9 h-9 rounded-full flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </button>

                                    <div x-show="confirming" x-cloak @keydown.escape.window="confirming = false"
                                        class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                                        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
                                            @click="confirming = false"></div>

                                        <div x-show="confirming" x-transition:enter="ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave="ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                            class="relative w-full max-w-sm bg-white rounded-3xl shadow-xl border border-slate-100 p-6 text-center">
                                            <div
                                                class="w-14 h-14 mx-auto rounded-full bg-red-50 flex items-center justify-center text-red-600 mb-4">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" />
                                                </svg>
                                            </div>
                                            <h3 class="text-base font-extrabold text-forest-900">Yakin ingin hapus?</h3>
                                            <p class="mt-1.5 text-sm text-slate-500">
                                                <span class="font-semibold text-slate-700">{{ $menu->nama }}</span>
                                                akan dihapus dari keranjang.
                                            </p>

                                            <div class="mt-5 flex gap-3">
                                                <button type="button" @click="confirming = false"
                                                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm py-2.5 rounded-full transition">
                                                    Batal
                                                </button>
                                                <button type="submit"
                                                    class="flex-1 bg-red-600 hover:bg-red-700 active:scale-[0.98] text-white font-bold text-sm py-2.5 rounded-full transition shadow-sm">
                                                    Ya, Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            <aside class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-5 sm:p-6 lg:sticky lg:top-28">
                <h2 class="font-extrabold text-forest-900 text-base mb-4">Ringkasan Pesanan</h2>

                <div class="mb-5">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tipe Pesanan</h3>
                        @if ($isFull)
                            <span
                                class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-200">Kantin
                                penuh</span>
                        @endif
                    </div>

                    @if ($isFull)
                        <p class="mb-2 text-[11px] text-amber-700">Makan di tempat dinonaktifkan. Silakan pilih bungkus /
                            dijemput.</p>
                    @endif

                    <form method="POST" action="{{ route('cart.order-type') }}">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                class="relative flex flex-col items-center gap-1.5 rounded-2xl border px-2 py-3 text-center transition
                                    {{ $isFull
                                        ? 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-300'
                                        : ($orderType === 'dine_in'
                                            ? 'cursor-pointer border-emerald-500 bg-emerald-50 text-emerald-700 ring-1 ring-emerald-500'
                                            : 'cursor-pointer border-slate-200 bg-white text-slate-600 hover:border-emerald-300') }}">
                                <input type="radio" name="order_type" value="dine_in" class="sr-only"
                                    @checked($orderType === 'dine_in') @disabled($isFull)
                                    onchange="this.form.submit()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 10h16M6 10V7a2 2 0 012-2h8a2 2 0 012 2v3M7 10v10m10-10v10"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                                </svg>
                                <span class="text-xs font-bold">Makan di Tempat</span>
                                @if ($isFull)
                                    <span class="text-[10px] font-semibold">Penuh</span>
                                @endif
                            </label>

                            <label
                                class="relative flex flex-col items-center gap-1.5 rounded-2xl border px-2 py-3 text-center transition
                                    {{ $orderType === 'takeaway'
                                        ? 'cursor-pointer border-emerald-500 bg-emerald-50 text-emerald-700 ring-1 ring-emerald-500'
                                        : 'cursor-pointer border-slate-200 bg-white text-slate-600 hover:border-emerald-300' }}">
                                <input type="radio" name="order_type" value="takeaway" class="sr-only"
                                    @checked($orderType === 'takeaway') onchange="this.form.submit()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="1.8" />
                                </svg>
                                <span class="text-xs font-bold">Bungkus / Diambil</span>
                            </label>
                        </div>
                    </form>

                    @if ($orderType)
                        <p class="mt-2 text-[11px] text-slate-400">Pilihan tersimpan:
                            <span class="font-semibold text-emerald-700">
                                {{ $orderType === 'dine_in' ? 'Makan di tempat' : 'Bungkus / dijemput' }}
                            </span>
                        </p>
                    @else
                        <p class="mt-2 text-[11px] text-slate-400">Pilih tipe pesanan sebelum checkout.</p>
                    @endif
                </div>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <dt>Jumlah item</dt>
                        <dd class="font-semibold text-slate-800">{{ $jumlahItem }}</dd>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <dt>Subtotal</dt>
                        <dd class="font-semibold text-slate-800">Rp{{ number_format($total, 0, ',', '.') }}</dd>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline">
                        <dt class="font-bold text-slate-800">Total</dt>
                        <dd class="text-xl font-extrabold text-forest-900">Rp{{ number_format($total, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>

                <button type="button" disabled title="Fitur checkout belum tersedia"
                    class="mt-5 w-full bg-emerald-600/50 text-white font-bold text-sm py-3 rounded-full cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    Checkout
                </button>
                <p class="mt-2 text-[11px] text-center text-slate-400">Pembayaran via Midtrans segera hadir.</p>

                <form method="POST" action="{{ route('cart.clear') }}" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full text-xs font-semibold text-slate-500 hover:text-red-600 py-2 rounded-full hover:bg-red-50 transition">
                        Kosongkan Keranjang
                    </button>
                </form>
            </aside>
        </div>

        <script>
            document.querySelectorAll('[data-cart-qty]').forEach(form => {
                const input = form.querySelector('input[name="qty"]');
                const label = form.querySelector('[data-qty-label]');
                const max = parseInt(input.max, 10) || 20;
                let timer = null;

                const submit = () => {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.submit();
                        }
                    }, 250);
                };

                form.querySelectorAll('[data-step]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        let v = parseInt(input.value, 10) + parseInt(btn.dataset.step, 10);
                        v = Math.min(max, Math.max(1, v));
                        input.value = v;
                        label.textContent = v;
                        submit();
                    });
                });
            });
        </script>
    @endif
</x-shop-layout>
