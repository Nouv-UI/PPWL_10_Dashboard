<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Published</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">{{ $publishedCount }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Draft</p>
                    <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $draftCount }}</p>
                </div>
            </div>
            <div class="mt-6 rounded-lg bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Skill Teratas</h3>
                    <a href="{{ route('post.projects.index') }}" class="text-sm text-indigo-600">Lihat project</a>
                </div>
                <div class="mt-4 divide-y">
                    @forelse ($topSkills as $skill)
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <a href="{{ route('post.skills.edit', $skill) }}" class="font-medium text-indigo-600 hover:text-indigo-900">{{ $skill->name }}</a>
                                <p class="text-xs text-gray-500">{{ $skill->category }}</p>
                            </div>
                            <span class="text-sm text-gray-600">{{ $skill->projects_count }} project</span>
                        </div>
                    @empty
                        <p class="py-3 text-sm text-gray-500">Belum ada skill.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
