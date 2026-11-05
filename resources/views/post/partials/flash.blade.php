@if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">Periksa kembali data yang dimasukkan.</div>
@endif
