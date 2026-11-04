<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\CanteenStatus;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
         $query = Menu::query()
            ->where('is_available', true)
            ->when($request->kategori, fn ($q, $k) => $q->where('kategori', $k))
            ->when($request->q, fn ($q, $s) => $q->where('nama', 'like', '%' . $s . '%'));

        match ($request->sort) {
            'termurah' => $query->orderBy('harga'),
            'termahal' => $query->orderByDesc('harga'),
            default    => $query->orderBy('kategori')->orderBy('nama'),
        };

        return view('catalog.index', [
            'menus'  => $query->get(),
            'isFull' => CanteenStatus::isFull(),
        ]);
    }
}
