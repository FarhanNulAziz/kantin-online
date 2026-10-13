<?php

namespace Database\Seeders;

use App\Models\CanteenStatus;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Kantin',
            'email' => 'admin_kantin@gmail.com',
            'no_hp' => '081234567890',
            'password' => Hash::make('admin1234'),
        ]);
        $admin->role = 'admin';
        $admin->save();
        User::create([
            'name' => 'Customer Contoh',
            'email' => 'customer@gmail.com',
            'no_hp' => '081298765432',
            'password' => Hash::make('customer1234'),
        ]);
        $menus = [
            ['nama' => 'Nasi Goreng',     'kategori' => 'makanan', 'harga' => 10000, 'stok' => 30],
            ['nama' => 'Mie Ayam',        'kategori' => 'makanan', 'harga' => 10000, 'stok' => 30],
            ['nama' => 'Ayam Geprek',     'kategori' => 'makanan', 'harga' => 16000, 'stok' => 25],
            ['nama' => 'Nasi Ayam Bakar', 'kategori' => 'makanan', 'harga' => 18000, 'stok' => 20],
            ['nama' => 'Es Teh',          'kategori' => 'minuman', 'harga' => 3000,  'stok' => 50],
            ['nama' => 'Es Jeruk',        'kategori' => 'minuman', 'harga' => 6000,  'stok' => 50],
            ['nama' => 'Kopi Susu',       'kategori' => 'minuman', 'harga' => 7000, 'stok' => 40],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
        CanteenStatus::create(['status' => 'tidak_penuh']);
    }
}
