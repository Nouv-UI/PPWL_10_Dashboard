<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Skill</h2>
    </x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            @include('post.partials.flash')
            <form method="POST" action="{{ route('post.skills.update', $skill) }}" class="space-y-5 rounded-lg bg-white p-6 shadow">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label for="name" value="Nama skill" />
                    <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $skill->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="category" value="Kategori" />
                    <x-text-input id="category" name="category" class="mt-1 block w-full" :value="old('category', $skill->category)" required />
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="description" value="Deskripsi" />
                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $skill->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                <x-primary-button>Simpan skill</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
