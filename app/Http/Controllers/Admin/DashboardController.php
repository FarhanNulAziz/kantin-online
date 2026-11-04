<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanteenStatus;
use App\Models\Menu;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $lowStockQuery = Menu::query()
            ->where('is_available', true)
            ->where('stok', '>', 0)
            ->where('stok', '<=', 5);

        return view('admin.dashboard', [
            'status' => CanteenStatus::current(),
            'isFull' => CanteenStatus::isFull(),
            'totalMenus' => Menu::count(),
            'availableMenus' => Menu::where('is_available', true)->count(),
            'lowStockMenus' => (clone $lowStockQuery)->count(),
            'outOfStockMenus' => Menu::where('stok', 0)->count(),
            'lowStockList' => $lowStockQuery->orderBy('stok')->take(5)->get(),
        ]);
    }
}
