<x-shop-layout>
    @php
        $jumlahItem = $items->sum('qty');
        $labelTipe = $orderType === 'dine_in' ? 'Makan di Tempat' : 'Bungkus / Diambil';
        $selectedMethod = old('payment_method') === 'online' || !$codAllowed ? 'online' : 'cod';
    @endphp

    <section class="mb-7 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-forest-900 tracking-tight">Checkout</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $jumlahItem }} item. Periksa pesananmu sebelum membuat pesanan.
            </p>
        </div>
        <a href="{{ route('cart.index') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
            Kembali ke keranjang
        </a>
    </section>

    @error('payment_method')
        <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-800 text-sm font-medium">{{ $message }}</div>
    @enderror

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">
        <section class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-extrabold text-forest-900 text-base">Ringkasan Item</h2>
            </div>

            <ul class="divide-y divide-slate-100">
                @foreach ($items as $item)
                            @php $menu = $item['menu']; @endphp
                            <li class="p-4 sm:p-5 flex items-center gap-3">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-slate-100 shrink-0">
                                    @if ($menu->gambar_url)
                                        <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <div
                                            class="w-full h-full flex items-center justify-center text-2xl font-extrabold text-slate-300">
                                            {{ strtoupper(substr($menu->nama, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-sm sm:text-base text-slate-800 truncate">{{ $menu->nama }}</h3>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border
                                                    {{ $menu->kategori === 'makanan'
                    ? 'bg-amber-100 text-amber-900 border-amber-200'
                    : 'bg-emerald-100 text-emerald-900 border-emerald-200' }}">
                                            {{ ucfirst($menu->kategori) }}
                                        </span>
                                        <span class="text-xs text-slate-500">
                                            {{ $item['qty'] }} × Rp{{ number_format($menu->harga, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Subtotal</p>
                                    <p class="font-extrabold text-forest-900 text-sm sm:text-base whitespace-nowrap">
                                        Rp{{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </li>
                @endforeach
            </ul>
        </section>

        <aside class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-5 sm:p-6 lg:sticky lg:top-28">
            <h2 class="font-extrabold text-forest-900 text-base mb-4">Pembayaran</h2>

            <div class="mb-5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tipe Pesanan</p>
                        <p class="font-bold text-sm text-slate-800 mt-0.5">{{ $labelTipe }}</p>
                    </div>
                    <a href="{{ route('cart.index') }}"
                        class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 whitespace-nowrap">Ubah</a>
                </div>
            </div>

            <form method="POST" action="{{ route('checkout.store') }}" x-data="{ metode: '{{ $selectedMethod }}' }">
                @csrf

                <fieldset>
                    <legend class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Metode Pembayaran
                    </legend>

                    <div class="space-y-2">
                        <label class="relative flex items-start gap-3 rounded-2xl border px-4 py-3 transition
                                {{ $codAllowed
    ? 'cursor-pointer border-slate-200 bg-white hover:border-emerald-300'
    : 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-400' }}">
                            <input type="radio" name="payment_method" value="cod" class="sr-only mt-1"
                                @checked($selectedMethod === 'cod') @disabled(!$codAllowed) x-model="metode">
                            <span class="mt-0.5 w-4 h-4 rounded-full border-2 shrink-0 flex items-center justify-center"
                                :class="metode === 'cod' ? 'border-emerald-600' : 'border-slate-300'">
                                <span class="w-2 h-2 rounded-full bg-emerald-600" x-show="metode === 'cod'"></span>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-sm font-bold">COD — Bayar di Kasir</span>
                                <span class="block text-[11px] mt-0.5">
                                    @if ($codAllowed)
                                        Bayar saat mengambil pesanan.
                                    @else
                                        Tidak tersedia karena total melebihi batas COD
                                        Rp{{ number_format($codMax, 0, ',', '.') }}.
                                    @endif
                                </span>
                            </span>
                        </label>

                        <label
                            class="relative flex items-start gap-3 cursor-pointer rounded-2xl border px-4 py-3 transition border-slate-200 bg-white hover:border-emerald-300">
                            <input type="radio" name="payment_method" value="online" class="sr-only mt-1"
                                @checked($selectedMethod === 'online') x-model="metode">
                            <span class="mt-0.5 w-4 h-4 rounded-full border-2 shrink-0 flex items-center justify-center"
                                :class="metode === 'online' ? 'border-emerald-600' : 'border-slate-300'">
                                <span class="w-2 h-2 rounded-full bg-emerald-600" x-show="metode === 'online'"></span>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-sm font-bold">Online</span>
                                <span class="block text-[11px] mt-0.5">Pembayaran online (Midtrans) aktif pada
                                    W7.</span>
                            </span>
                        </label>
                    </div>
                </fieldset>
                <dl class="mt-5 space-y-3 text-sm">
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
                        <dd class="text-xl font-extrabold text-forest-900">
                            Rp{{ number_format($total, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                <button type="submit"
                    class="mt-5 w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-sm py-3 rounded-full transition shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" />
                    </svg>
                    Buat Pesanan
                </button>
            </form>
        </aside>
    </div>
</x-shop-layout>
