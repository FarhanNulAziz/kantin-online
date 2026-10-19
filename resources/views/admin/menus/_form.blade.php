@csrf

<div>
    <x-input-label for="nama" value="Nama menu" />
    <x-text-input id="nama" name="nama" type="text" class="block mt-1 w-full" :value="old('nama', $menu->nama ?? '')"
        required />
    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="kategori" value="Kategori" />
    <select id="kategori" name="kategori" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
        @foreach (['makanan' => 'Makanan', 'minuman' => 'Minuman'] as $value => $label)
            <option value="{{ $value }}" @selected(old('kategori', $menu->kategori ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="harga" value="Harga (Rp)" />
    <x-text-input id="harga" name="harga" type="number" min="500" class="block mt-1 w-full" :value="old('harga', $menu->harga ?? '')" required />
    <x-input-error :messages="$errors->get('harga')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="stok" value="Stok" />
    <x-text-input id="stok" name="stok" type="number" min="0" class="block mt-1 w-full" :value="old('stok', $menu->stok ?? 0)" required />
    <x-input-error :messages="$errors->get('stok')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="gambar" value="Foto menu (maks 2 MB)" />

    @if (!empty($menu->gambar_url))
        <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}" class="mt-2 h-32 w-32 object-cover rounded border">
        <p class="text-xs text-gray-500 mt-1">Pilih file baru untuk mengganti foto ini.</p>
    @endif

    <input id="gambar" name="gambar" type="file" accept="image/png,image/jpeg,image/webp"
        class="block mt-2 w-full text-sm">
    <x-input-error :messages="$errors->get('gambar')" class="mt-2" />
</div>

<label class="inline-flex items-center mt-4">
    <input type="checkbox" name="is_available" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm"
        @checked(old('is_available', $menu->is_available ?? true))>
    <span class="ms-2 text-sm text-gray-600">Tersedia</span>
</label>

<div class="flex items-center gap-3 mt-6">
    <x-primary-button>Simpan</x-primary-button>
    <a href="{{ route('admin.menus.index') }}" class="text-sm text-gray-600 underline">Batal</a>
</div>
