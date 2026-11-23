<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Collection;

class Cart
{
    /** @return array<int, array{qty: int, note: ?string}> [menu_id => ['qty' => int, 'note' => ?string]] */
    public static function all(): array
    {
        $cart = session('cart', []);

        if (! is_array($cart)) {
            return [];
        }

        return collect($cart)
            ->mapWithKeys(function ($entry, $menuId): array {
                if (is_array($entry)) {
                    $qty = (int) ($entry['qty'] ?? 0);
                    $note = $entry['note'] ?? null;
                } else {
                    $qty = is_numeric($entry) ? (int) $entry : 0;
                    $note = null;
                }

                if ($qty < 1) {
                    return [];
                }

                return [(int) $menuId => [
                    'qty' => $qty,
                    'note' => is_string($note) && $note !== '' ? mb_substr($note, 0, 100) : null,
                ]];
            })
            ->all();
    }

    public static function count(): int
    {
        return (int) collect(self::all())->sum('qty');
    }

    public static function items(): Collection
    {
        $cart = self::all();
        $menus = Menu::whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)
            ->filter(fn (array $entry, int $menuId) => $menus->has($menuId))
            ->map(fn (array $entry, int $menuId) => [
                'menu' => $menus[$menuId],
                'qty' => $entry['qty'],
                'note' => $entry['note'],
                'subtotal' => $menus[$menuId]->harga * $entry['qty'],
            ]);
    }

    public static function subtotal(): int
    {
        return (int) self::items()->sum('subtotal');
    }

    public static function adminFee(): int
    {
        return (int) config('kantin.admin_fee');
    }

    public static function grandTotal(int $subtotal): int
    {
        return $subtotal + self::adminFee();
    }

    public static function setNote(Menu $menu, ?string $note): void
    {
        $cart = self::all();

        if (! isset($cart[$menu->id])) {
            return;
        }

        $cart[$menu->id]['note'] = $note;

        session(['cart' => $cart]);
    }
}
