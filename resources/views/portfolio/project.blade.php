<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $project->name }}</h2>
    </x-slot>

    <div class="py-8">
        <article class="max-w-5xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">{{ $project->category?->name }}</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
                @if ($project->thumbnail)
                    <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->name }}" class="mt-5 max-h-[32rem] w-full rounded object-cover">
                @endif
                @if ($project->description)
                    <p class="mt-5 whitespace-pre-line text-gray-700">{{ $project->description }}</p>
                @endif
            </section>

            @if ($project->images->isNotEmpty())
                <section class="rounded-lg bg-white p-6 shadow">
                    <h3 class="font-semibold text-gray-900">Gallery</h3>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($project->images as $image)
                            <figure>
                                <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $image->caption ?: $project->name }}" class="h-48 w-full rounded object-cover">
                                @if ($image->caption)
                                    <figcaption class="mt-2 text-sm text-gray-600">{{ $image->caption }}</figcaption>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                </section>
            @endif
        </article>
    </div>
</x-app-layout>
