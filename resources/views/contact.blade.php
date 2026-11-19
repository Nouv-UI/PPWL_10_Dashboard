<x-app-layout>
    <div class="mx-auto mt-6 max-w-xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h1 class="mb-4 text-3xl font-bold text-gray-900">Hubungi Saya</h1>

        @if (session('success'))
            <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="mb-1 block text-sm font-semibold">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" maxlength="100" required
                    class="w-full rounded border px-3 py-2">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-semibold">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" maxlength="100" required
                    class="w-full rounded border px-3 py-2">
                @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subject" class="mb-1 block text-sm font-semibold">Subjek</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" maxlength="200"
                    class="w-full rounded border px-3 py-2">
                @error('subject')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="mb-1 block text-sm font-semibold">Pesan</label>
                <textarea name="message" id="message" rows="5" minlength="10" required
                    class="w-full rounded border px-3 py-2">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white">Kirim Pesan</button>
        </form>
    </div>
</x-app-layout>
