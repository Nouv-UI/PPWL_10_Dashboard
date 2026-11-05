<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Project</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            @include('post.partials.flash')
            <section class="rounded-lg bg-white p-6 shadow">
                <form method="POST" action="{{ route('post.projects.update', $project) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="title" value="Judul" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $project->title)" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $project->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach (['draft' => 'Draft', 'published' => 'Published'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $project->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="thumbnail" value="Thumbnail baru (opsional)" />
                        <input id="thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-gray-600">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, atau WEBP, maksimal 4 MB.</p>
                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Skills" />
                        <div class="mt-2 grid gap-2 sm:grid-cols-2">
                            @foreach ($skills as $skill)
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="skill_ids[]" value="{{ $skill->id }}" @checked($project->skills->contains($skill->id))>
                                    {{ $skill->name }} <span class="text-gray-400">({{ $skill->category }})</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('skill_ids.*')" class="mt-2" />
                    </div>
                    <x-primary-button>Simpan project</x-primary-button>
                </form>
            </section>

            <section class="rounded-lg bg-white p-6 shadow">
                <h3 class="font-semibold text-gray-900">Tambah Gallery</h3>
                <form method="POST" action="{{ route('post.projects.gallery.store', $project) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                    @csrf
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-600">
                    <p class="text-xs text-gray-500">Lebar minimal 800 px, maksimal 4 MB per file, total maksimal 10 gambar/project.</p>
                    <x-input-error :messages="$errors->get('images')" class="mt-2" />
                    <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
                    <x-primary-button>Upload gallery</x-primary-button>
                </form>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($project->galleries as $gallery)
                        <div class="rounded border p-3">
                            <img src="{{ Storage::disk('public')->url($gallery->image_path) }}" alt="{{ $gallery->caption ?: 'Gallery project' }}" class="h-32 w-full rounded object-cover">
                            <form method="POST" action="{{ route('post.gallery.caption.update', $gallery) }}" class="mt-2 flex gap-2">
                                @csrf
                                @method('PUT')
                                <input name="caption" value="{{ $gallery->caption }}" maxlength="255" class="min-w-0 flex-1 rounded border-gray-300 text-sm" placeholder="Caption">
                                <button class="text-sm font-medium text-indigo-600">Simpan</button>
                            </form>
                            <form method="POST" action="{{ route('post.gallery.destroy', $gallery) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm text-red-600" onclick="return confirm('Hapus gambar ini?')">Hapus gambar</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 sm:col-span-2 lg:col-span-3">Belum ada gallery.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
