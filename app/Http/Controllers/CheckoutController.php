<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckoutRequest;
use App\Models\CanteenStatus;
use App\Models\Menu;
use App\Models\Order;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $checkout = $this->resolveCheckout();

        if ($checkout instanceof RedirectResponse) {
            return $checkout;
        }

        $codMax = (int) config('kantin.cod_max_amount');

        return view('checkout.show', [
            'items' => $checkout['items'],
            'total' => $checkout['total'],
            'orderType' => $checkout['orderType'],
            'codMax' => $codMax,
            'codAllowed' => $checkout['total'] <= $codMax,
        ]);
    }

    public function store(StoreCheckoutRequest $request): RedirectResponse
    {
        $checkout = $this->resolveCheckout();

        if ($checkout instanceof RedirectResponse) {
            return $checkout;
        }

        $paymentMethod = $request->validated('payment_method');
        $codMax = (int) config('kantin.cod_max_amount');

        if ($paymentMethod === 'cod' && $checkout['total'] > $codMax) {
            return redirect()->route('checkout.show')->with(
                'error',
                'Total pesanan melebihi batas COD Rp'.number_format($codMax, 0, ',', '.').'. Silakan pilih pembayaran online.'
            );
        }

        $result = DB::transaction(function () use ($checkout, $paymentMethod, $codMax): array {
            $cart = Cart::all();

            // Kunci baris menu agar stok tidak berubah selama transaksi.
            $menus = Menu::whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = [];
            $issues = [];
            $total = 0;

            foreach ($cart as $menuId => $qty) {
                $menu = $menus->get($menuId);

                if (! $menu) {
                    $issues[] = 'Ada menu di keranjang yang sudah tidak tersedia.';

                    continue;
                }

                if (! $menu->is_available) {
                    $issues[] = "{$menu->nama} sedang tidak tersedia.";

                    continue;
                }

                if ($menu->stok < $qty) {
                    $issues[] = "Stok {$menu->nama} tidak mencukupi (tersisa {$menu->stok}).";

                    continue;
                }

                $total += $menu->harga * $qty;
                $items[] = [
                    'menu' => $menu,
                    'qty' => (int) $qty,
                    'harga_satuan' => $menu->harga,
                ];
            }

            if ($issues !== []) {
                return ['error' => implode(' ', $issues)];
            }

            if ($total === 0) {
                return ['error' => 'Keranjang belanja masih kosong.'];
            }

            // Harga bisa berubah sejak halaman checkout dibuka; validasi ulang batas COD.
            if ($paymentMethod === 'cod' && $total > $codMax) {
                return ['error' => 'Total pesanan melebihi batas COD Rp'.number_format($codMax, 0, ',', '.').'. Silakan pilih pembayaran online.'];
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_type' => $checkout['orderType'],
                'payment_method' => $paymentMethod,
                'total_harga' => $total,
                'payment_status' => $paymentMethod === 'cod' ? 'cod_pending' : 'pending',
                'order_status' => 'baru',
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'menu_id' => $item['menu']->id,
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                ]);

                $item['menu']->decrement('stok', $item['qty']);
            }

            return ['order' => $order];
        });

        if (isset($result['error'])) {
            return redirect()->route('cart.index')->with('error', $result['error']);
        }

        // TODO W7: integrasi Midtrans Snap untuk pesanan online dengan payment_status = pending.
        session()->forget(['cart', 'order_type']);

        return redirect()->route('orders.show', $result['order'])
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    /**
     * Validasi kondisi checkout dari session dan data menu di database.
     *
     * @return array{items: Collection, total: int, orderType: string}|RedirectResponse
     */
    private function resolveCheckout(): array|RedirectResponse
    {
        $cart = Cart::all();

        if ($cart === []) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja masih kosong.');
        }

        $orderType = session('order_type');

        if (! in_array($orderType, ['dine_in', 'takeaway'], true)) {
            return redirect()->route('cart.index')->with('error', 'Pilih tipe pesanan terlebih dahulu.');
        }

        if ($orderType === 'dine_in' && CanteenStatus::isFull()) {
            session()->forget('order_type');

            return redirect()->route('cart.index')->with('error', 'Kantin sedang penuh. Silakan pilih bungkus / dijemput.');
        }

        $items = Cart::items();

        if ($items->count() !== count($cart)) {
            return redirect()->route('cart.index')->with('error', 'Ada menu di keranjang yang sudah tidak tersedia. Perbarui keranjang Anda.');
        }

        $issues = [];

        foreach ($items as $item) {
            $menu = $item['menu'];

            if (! $menu->is_available) {
                $issues[] = "{$menu->nama} sedang tidak tersedia.";
            } elseif ($item['qty'] > $menu->stok) {
                $issues[] = "Stok {$menu->nama} tidak mencukupi (tersisa {$menu->stok}).";
            }
        }

        if ($issues !== []) {
            return redirect()->route('cart.index')->with('error', implode(' ', $issues));
        }

        return [
            'items' => $items,
            'total' => (int) $items->sum('subtotal'),
            'orderType' => $orderType,
        ];
    }
}
