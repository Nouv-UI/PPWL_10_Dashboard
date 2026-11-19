@props(['label', 'value', 'color' => 'text-blue-600'])

<div class="rounded-lg bg-white p-6 shadow-sm">
    <div class="text-sm uppercase tracking-wider text-gray-500">{{ $label }}</div>
    <div class="mt-2 text-3xl font-bold {{ $color }}">{{ $value }}</div>
</div>
