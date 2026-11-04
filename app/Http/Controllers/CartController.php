<?php

namespace App\Http\Controllers;

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
        return view('cart.index', [
            'items' => Cart::items(),
            'total' => Cart::total(),
        ]);
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
