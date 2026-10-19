<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ubah Menu</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.menus.update', $menu) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @include('admin.menus._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
