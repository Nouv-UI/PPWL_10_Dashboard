<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profil Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">
                    Informasi Pengguna
                </h3>

                <p>
                    <strong>Nama:</strong>
                    {{ Auth::user()->name }}
                </p>

                <p>
                    <strong>Email:</strong>
                    {{ Auth::user()->email }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>