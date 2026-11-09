@csrf

@php
    $inputClass =
        'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500';
    $labelClass = 'block text-sm font-semibold text-slate-700 mb-1.5';
@endphp

<div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_300px] gap-6">
    <div>
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" />
                </svg>
            </div>
            <h2 class="text-sm font-extrabold text-forest-900">Informasi Menu</h2>
        </div>

        <div class="space-y-4">
            <div>
                <label for="nama" class="{{ $labelClass }}">Nama menu</label>
                <input id="nama" name="nama" type="text" value="{{ old('nama', $menu->nama ?? '') }}"
                    placeholder="Contoh: Nasi Goreng Spesial" required class="{{ $inputClass }}">
                @error('nama')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kategori" class="{{ $labelClass }}">Kategori</label>
                    <select id="kategori" name="kategori" required class="{{ $inputClass }}">
                        @foreach (['makanan' => 'Makanan', 'minuman' => 'Minuman'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('kategori', $menu->kategori ?? '') === $value)>{{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga" class="{{ $labelClass }}">Harga (Rp)</label>
                    <input id="harga" name="harga" type="number" min="500"
                        value="{{ old('harga', $menu->harga ?? '') }}" placeholder="10000" required
                        class="{{ $inputClass }}">
                    @error('harga')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="stok" class="{{ $labelClass }}">Stok</label>
                <input id="stok" name="stok" type="number" min="0" value="{{ old('stok', $menu->stok ?? 0) }}"
                    placeholder="0" required class="{{ $inputClass }}">
                <p class="mt-1.5 text-xs text-slate-400">Stok 0 akan menampilkan menu sebagai habis di katalog.</p>
                @error('stok')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <aside class="space-y-4">
        <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4">
            <label for="gambar" class="{{ $labelClass }}">Foto menu</label>

            @if (!empty($menu->gambar_url))
                <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}"
                    class="w-full h-36 object-cover rounded-xl border border-slate-100 mb-3">
                <p class="text-xs text-slate-500 mb-2">Pilih file baru untuk mengganti foto ini.</p>
            @else
                <div
                    class="w-full h-36 rounded-xl border border-dashed border-slate-200 bg-white flex flex-col items-center justify-center text-slate-400 mb-3">
                    <svg class="w-7 h-7 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                    </svg>
                    <span class="text-xs font-medium">Belum ada foto</span>
                </div>
            @endif

            <input id="gambar" name="gambar" type="file" accept="image/png,image/jpeg,image/webp"
                class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100">
            <p class="mt-1.5 text-[11px] text-slate-400">PNG, JPG, atau WEBP. Maksimal 2 MB.</p>
            @error('gambar')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="is_available" value="1"
                    class="mt-0.5 rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500"
                    @checked(old('is_available', $menu->is_available ?? true))>
                <span>
                    <span class="block text-sm font-bold text-slate-700">Tersedia di katalog</span>
                    <span class="block text-xs text-slate-500 mt-0.5">Matikan untuk menyembunyikan menu tanpa
                        menghapusnya.</span>
                </span>
            </label>
        </div>
    </aside>
</div>

<div class="flex items-center gap-3 mt-7 pt-5 border-t border-slate-100">
    <button type="submit"
        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-sm px-5 py-2.5 rounded-full transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        Simpan
    </button>
    <a href="{{ route('admin.menus.index') }}"
        class="text-sm font-semibold text-slate-500 hover:text-slate-700 px-3 py-2 transition">Batal</a>
</div>
