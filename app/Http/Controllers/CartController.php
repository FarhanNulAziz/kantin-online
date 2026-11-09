<?php

namespace App\Http\Controllers;

use App\Models\CanteenStatus;
use App\Models\Menu;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    private const MAX_PER_ITEM = 20;

    public function index(): View
    {
        $isFull = CanteenStatus::isFull();
        $orderType = session('order_type');

        if (! in_array($orderType, ['dine_in', 'takeaway'], true)) {
            $orderType = null;
        }

        if ($isFull && $orderType === 'dine_in') {
            session()->forget('order_type');
            $orderType = null;
        }

        return view('cart.index', [
            'items' => Cart::items(),
            'total' => Cart::total(),
            'isFull' => $isFull,
            'orderType' => $orderType,
        ]);
    }

    public function updateOrderType(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_type' => ['required', 'in:dine_in,takeaway'],
        ]);

        if ($data['order_type'] === 'dine_in' && CanteenStatus::isFull()) {
            return back()->with('error', 'Kantin sedang penuh. Silakan pilih bungkus / dijemput.');
        }

        session(['order_type' => $data['order_type']]);

        $label = $data['order_type'] === 'dine_in' ? 'Makan di tempat' : 'Bungkus / dijemput';

        return back()->with('success', "Tipe pesanan disimpan: {$label}.");
    }

    public function add(Request $request, Menu $menu): RedirectResponse
    {
        $request->validate(['qty' => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_PER_ITEM]]);

        if (! $menu->is_available || $menu->stok < 1) {
            return back()->with('error', "{$menu->nama} sedang tidak tersedia.");
        }

        $cart = Cart::all();
        $limit = min($menu->stok, self::MAX_PER_ITEM);
        $new = ($cart[$menu->id] ?? 0) + (int) $request->input('qty', 1);

        $cart[$menu->id] = min($new, $limit);
        session(['cart' => $cart]);

        $msg = $new > $limit
            ? "Jumlah {$menu->nama} dibatasi maksimal {$limit}."
            : "{$menu->nama} ditambahkan ke keranjang.";

        return back()->with('success', $msg);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $data = $request->validate(['qty' => ['required', 'integer', 'min:0', 'max:' . self::MAX_PER_ITEM]]);

        $cart = Cart::all();

        if ($data['qty'] === 0) {
            unset($cart[$menu->id]);
        } else {
            $cart[$menu->id] = min($data['qty'], $menu->stok, self::MAX_PER_ITEM);
        }

        session(['cart' => $cart]);

        return back();
    }

    public function remove(Menu $menu): RedirectResponse
    {
        $cart = Cart::all();
        unset($cart[$menu->id]);
        session(['cart' => $cart]);

        return back()->with('success', "{$menu->nama} dihapus dari keranjang.");
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return back()->with('success', 'Keranjang dikosongkan.');
    }
}
