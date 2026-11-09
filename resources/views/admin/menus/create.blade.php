<x-admin-layout title="Tambah Menu · Kantin Online">
    <section class="mb-7 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-forest-900 tracking-tight">Tambah Menu</h1>
            <p class="text-sm text-slate-500 mt-1">Lengkapi informasi menu baru untuk ditampilkan di katalog.</p>
        </div>
        <a href="{{ route('admin.menus.index') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" />
            </svg>
            Kembali ke Kelola Menu
        </a>
    </section>

    <div class="max-w-4xl">
        <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.menus.store') }}" enctype="multipart/form-data">
                @include('admin.menus._form')
            </form>
        </div>
    </div>
</x-admin-layout>
