<x-shop-layout>
    @php
        $nomorPesanan = '#' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $labelTipe = $order->order_type === 'dine_in' ? 'Makan di Tempat' : 'Bungkus / Diambil';
        $labelMetode = $order->payment_method === 'cod' ? 'COD — Bayar di Kasir' : 'Online';

        $labelPembayaran = [
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Lunas',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            'cod_pending' => 'Bayar di Kasir',
        ][$order->payment_status] ?? ucfirst($order->payment_status);

        $labelStatus = [
            'baru' => 'Baru',
            'diproses' => 'Diproses',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ][$order->order_status] ?? ucfirst($order->order_status);

        $warnaPembayaran = match ($order->payment_status) {
            'paid' => 'bg-emerald-100 text-emerald-900 border-emerald-200',
            'failed', 'expired' => 'bg-red-100 text-red-800 border-red-200',
            'cod_pending' => 'bg-amber-100 text-amber-900 border-amber-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };

        $warnaStatus = match ($order->order_status) {
            'selesai' => 'bg-emerald-100 text-emerald-900 border-emerald-200',
            'dibatalkan' => 'bg-red-100 text-red-800 border-red-200',
            'siap_diambil', 'diproses' => 'bg-amber-100 text-amber-900 border-amber-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    @endphp

    <section class="mb-7 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Pesanan {{ $nomorPesanan }}</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-forest-900 tracking-tight mt-1">Detail Pesanan</h1>
            <p class="text-sm text-slate-500 mt-1">Dibuat {{ $order->created_at->format('d M Y, H:i') }} WIB.</p>
        </div>
        <a href="{{ route('catalog') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
            Kembali ke menu
        </a>
    </section>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-100 text-emerald-900 text-sm font-medium">{{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">
        <section class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                <h2 class="font-extrabold text-forest-900 text-base">Item Pesanan</h2>
                <span class="text-xs text-slate-500">{{ $order->items->sum('qty') }} item</span>
            </div>

            <ul class="divide-y divide-slate-100">
                @foreach ($order->items as $item)
                    @php $menu = $item->menu; @endphp
                    <li class="p-4 sm:p-5 flex items-center gap-3">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-slate-100 shrink-0">
                            @if ($menu?->gambar_url)
                                <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}" class="w-full h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full flex items-center justify-center text-2xl font-extrabold text-slate-300">
                                    {{ strtoupper(substr($menu?->nama ?? '?', 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-sm sm:text-base text-slate-800 truncate">
                                {{ $menu?->nama ?? 'Menu tidak tersedia' }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $item->qty }} × Rp{{ number_format($item->harga_satuan, 0, ',', '.') }}
                            </p>
                            @if ($item->catatan)
                                <p class="mt-2 text-[11px] text-slate-600 rounded-lg bg-slate-50 px-2.5 py-1.5">
                                    <span class="font-bold text-slate-500">Catatan:</span> {{ $item->catatan }}
                                </p>
                            @endif
                        </div>

                        <div class="text-right shrink-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Subtotal</p>
                            <p class="font-extrabold text-forest-900 text-sm sm:text-base whitespace-nowrap">
                                Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="px-5 py-4 border-t border-slate-100 space-y-2">
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Subtotal</span>
                    <span class="font-semibold text-slate-800">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Biaya admin</span>
                    <span class="font-semibold text-slate-800">Rp{{ number_format($order->biaya_admin, 0, ',', '.') }}</span>
                </div>
                <div class="pt-2 border-t border-slate-100 flex justify-between items-baseline">
                    <span class="font-bold text-slate-800">Total</span>
                    <span class="text-xl font-extrabold text-forest-900">
                        Rp{{ number_format($order->total_harga, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </section>

        <aside class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-5 sm:p-6 lg:sticky lg:top-28">
            <h2 class="font-extrabold text-forest-900 text-base mb-4">Informasi Pesanan</h2>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Nomor pesanan</dt>
                    <dd class="font-bold text-slate-800">{{ $nomorPesanan }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Tipe pesanan</dt>
                    <dd class="font-semibold text-slate-800 text-right">{{ $labelTipe }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Metode pembayaran</dt>
                    <dd class="font-semibold text-slate-800 text-right">{{ $labelMetode }}</dd>
                </div>
                <div class="flex justify-between items-center gap-3">
                    <dt class="text-slate-500">Status pembayaran</dt>
                    <dd>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $warnaPembayaran }}">
                            {{ $labelPembayaran }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between items-center gap-3">
                    <dt class="text-slate-500">Status pesanan</dt>
                    <dd>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $warnaStatus }}">
                            {{ $labelStatus }}
                        </span>
                    </dd>
                </div>
            </dl>

            @if ($order->payment_method === 'online' && $order->payment_status === 'pending')
                <div class="mt-5 space-y-3">
                    <button id="pay-button" type="button"
                        class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-2xl bg-forest-900 hover:bg-forest-800 active:bg-forest-700 text-white font-extrabold text-sm transition-colors shadow-md shadow-emerald-900/10">
                        Bayar Sekarang
                    </button>
                    <p id="pay-message" class="text-xs text-slate-600"></p>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Status pembayaran akan diperbarui otomatis setelah pembayaran dikonfirmasi.
                    </p>
                </div>
            @elseif ($order->payment_method === 'cod')
                <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
                    <p class="text-sm font-bold text-amber-900">Bayar di kasir</p>
                    <p class="text-[11px] text-amber-800 mt-1 leading-relaxed">
                        Silakan bayar di kasir saat mengambil pesanan. Tunjukkan nomor pesanan
                        <span class="font-bold">{{ $nomorPesanan }}</span>.
                    </p>
                    <p class="mt-2 text-sm font-bold text-amber-900">
                        Nominal di kasir: Rp{{ number_format($order->total_harga, 0, ',', '.') }}
                    </p>
                </div>
            @endif
        </aside>
    </div>

    @if ($order->payment_method === 'online' && $order->payment_status === 'pending')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
        <script>
            (function () {
                const payButton = document.getElementById('pay-button');
                const payMessage = document.getElementById('pay-message');
                if (!payButton) return;

                payButton.addEventListener('click', async function () {
                    payButton.disabled = true;
                    payMessage.textContent = 'Memproses pembayaran...';

                    try {
                        const response = await fetch('{{ route('orders.pay', $order) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok || !data.token) {
                            payMessage.textContent = data.message || 'Gagal memulai pembayaran. Silakan coba lagi.';
                            payButton.disabled = false;
                            return;
                        }

                        window.snap.pay(data.token, {
                            onSuccess: function () {
                                // TODO W8: status final diperbarui oleh webhook.
                                payMessage.textContent = 'Menunggu konfirmasi pembayaran...';
                                window.location.reload();
                            },
                            onPending: function () {
                                // TODO W8: status final diperbarui oleh webhook.
                                payMessage.textContent = 'Pembayaran tertunda. Memuat ulang...';
                                window.location.reload();
                            },
                            onError: function () {
                                // TODO W8: status final diperbarui oleh webhook.
                                payMessage.textContent = 'Terjadi kesalahan pembayaran. Memuat ulang...';
                                window.location.reload();
                            },
                            onClose: function () {
                                // TODO W8: status final diperbarui oleh webhook.
                                payMessage.textContent = 'Jendela pembayaran ditutup. Memuat ulang...';
                                window.location.reload();
                            }
                        });
                    } catch (e) {
                        payMessage.textContent = 'Gagal memulai pembayaran. Silakan coba lagi.';
                        payButton.disabled = false;
                    }
                });
            })();
        </script>
    @endif
</x-shop-layout>
