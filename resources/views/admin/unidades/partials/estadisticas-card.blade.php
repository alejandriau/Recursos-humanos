@props([
    'color' => 'blue',
    'icon' => 'building',
    'title' => 'Título',
    'value' => 0,
    'tooltip' => ''
])

@php
    $colorClasses = [
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'hover' => 'hover:bg-blue-200'],
        'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'hover' => 'hover:bg-green-200'],
        'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'hover' => 'hover:bg-red-200'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'hover' => 'hover:bg-purple-200'],
        'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'hover' => 'hover:bg-yellow-200'],
        'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'hover' => 'hover:bg-indigo-200'],
    ];

    $selectedColor = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow duration-200 {{ $selectedColor['hover'] }}"
     @if($tooltip) title="{{ $tooltip }}" @endif>
    <div class="flex items-center">
        <div class="p-2 rounded-full {{ $selectedColor['bg'] }} {{ $selectedColor['text'] }}">
            <i class="fas fa-{{ $icon }}"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-lg font-semibold text-gray-900">{{ $value }}</p>
        </div>
    </div>
</div>
