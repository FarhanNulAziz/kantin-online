<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Menu</h2>
            <a href="{{ route('admin.menus.create') }}"
                class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase rounded-md">
                + Tambah Menu
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-4 flex gap-2 text-sm">
                @php
                    $aktif = request('kategori');
                    $base = 'px-4 py-2 rounded-md border text-sm font-medium transition';
                    $on = 'bg-gray-800 text-white border-gray-800';
                    $off = 'bg-white text-gray-600 border-gray-300 hover:bg-gray-100';
                @endphp

                <div class="mb-4 flex gap-2">
                    <a href="{{ route('admin.menus.index') }}" class="{{ $base }} {{ !$aktif ? $on : $off }}">Semua</a>

                    <a href="{{ route('admin.menus.index', ['kategori' => 'makanan']) }}"
                        class="{{ $base }} {{ $aktif === 'makanan' ? $on : $off }}">Makanan</a>

                    <a href="{{ route('admin.menus.index', ['kategori' => 'minuman']) }}"
                        class="{{ $base }} {{ $aktif === 'minuman' ? $on : $off }}">Minuman</a>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="p-3">Foto</th>
                            <th class="p-3">Nama</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Harga</th>
                            <th class="p-3">Stok</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menus as $menu)
                            <tr class="border-t">
                                <td class="p-3">
                                    @if ($menu->gambar_url)
                                        <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}"
                                            class="h-12 w-12 object-cover rounded">
                                    @else
                                        <div
                                            class="h-12 w-12 rounded bg-gray-200 text-gray-500 flex items-center justify-center font-semibold">
                                            {{ strtoupper(substr($menu->nama, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3">{{ $menu->nama }}</td>
                                <td class="p-3 capitalize">{{ $menu->kategori }}</td>
                                <td class="p-3">Rp{{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td class="p-3">{{ $menu->stok }}</td>
                                <td class="p-3">
                                    <form method="POST" action="{{ route('admin.menus.toggle', $menu) }}">
                                        @csrf @method('PATCH')
                                        <button
                                            class="px-2 py-1 rounded text-xs
                                                    {{ $menu->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="p-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.menus.edit', $menu) }}"
                                            class="inline-flex items-center px-3 py-1.5 rounded-md bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">
                                            Ubah
                                        </a>

                                        <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}"
                                            onsubmit="return confirm('Hapus menu {{ $menu->nama }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 rounded-md bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500">Belum ada menu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $menus->links() }}</div>
        </div>
    </div>
</x-app-layout>
