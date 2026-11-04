<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Collection;

class Cart
{
    /** @return array<int,int> [menu_id => qty] */
    public static function all(): array
    {
        return session('cart', []);
    }

    public static function count(): int
    {
        return array_sum(self::all());
    }

    public static function items(): Collection
    {
        $cart = self::all();
        $menus = Menu::whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)
            ->filter(fn ($qty, $id) => $menus->has($id))
            ->map(fn ($qty, $id) => [
                'menu'     => $menus[$id],
                'qty'      => $qty,
                'subtotal' => $menus[$id]->harga * $qty,
            ]);
    }

    public static function total(): int
    {
        return self::items()->sum('subtotal');
    }
}
