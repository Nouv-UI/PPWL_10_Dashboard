<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Projects</h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Dashboard</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('post.partials.flash')
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($projects as $project)
                    <article class="overflow-hidden rounded-lg bg-white shadow">
                        @if ($project->thumbnail)
                            <img src="{{ Storage::disk('public')->url($project->thumbnail) }}" alt="{{ $project->title }}" class="h-48 w-full object-cover">
                        @else
                            <div class="flex h-48 items-center justify-center bg-gray-200 text-gray-500">No thumbnail</div>
                        @endif
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-semibold text-gray-900">{{ $project->title }}</h3>
                                <span class="rounded-full px-2 py-1 text-xs {{ $project->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ ucfirst($project->status) }}</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">{{ Str::limit($project->description, 110) }}</p>
                            <p class="mt-3 text-xs text-gray-500">{{ $project->galleries->count() }} gallery · {{ $project->skills->count() }} skill</p>
                            <a href="{{ route('post.projects.edit', $project) }}" class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-900">Edit project</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg bg-white p-8 text-gray-600 shadow md:col-span-2 lg:col-span-3">Belum ada project.</div>
                @endforelse
            </div>
            <div class="mt-6">{{ $projects->links() }}</div>
        </div>
    </div>
</x-app-layout>
