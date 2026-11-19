<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Portfolio Projects</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($projects as $project)
                    <article class="overflow-hidden rounded-lg bg-white shadow">
                        @if ($project->thumbnail)
                            <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->name }}" class="h-48 w-full object-cover">
                        @endif
                        <div class="p-5">
                            <p class="text-xs text-gray-500">{{ $project->category?->name }}</p>
                            <h3 class="mt-1 font-semibold text-gray-900">{{ $project->name }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ Str::limit($project->description, 120) }}</p>
                            <a href="{{ route('projects.show', $project) }}" class="mt-4 inline-block text-sm font-medium text-indigo-600">Lihat detail</a>
                        </div>
                    </article>
                @empty
                    <p class="text-gray-600">Belum ada project yang dipublikasikan.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
