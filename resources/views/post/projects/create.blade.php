<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Project</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('post.partials.flash')
            <section class="rounded-lg bg-white p-6 shadow">
                <form method="POST" action="{{ route('post.projects.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nama project" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="category_id" value="Kategori" />
                        <select id="category_id" name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                            <option value="published" @selected(old('status') === 'published')>Published</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="thumbnail" value="Thumbnail (opsional)" />
                        <input id="thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-gray-600">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, atau WEBP, minimal 400×300 px dan maksimal 2 MB.</p>
                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                    </div>
                    <x-primary-button>Simpan project</x-primary-button>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
