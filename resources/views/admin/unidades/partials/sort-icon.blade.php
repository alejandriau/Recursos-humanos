@props([
    'field' => 'denominacion'
])

@php
    $currentOrder = request('orden', 'denominacion');
    $currentDirection = request('direccion', 'asc');
@endphp

<span class="ml-1">
    @if($currentOrder === $field)
        @if($currentDirection === 'asc')
            <i class="fas fa-sort-up text-blue-500"></i>
        @else
            <i class="fas fa-sort-down text-blue-500"></i>
        @endif
    @else
        <i class="fas fa-sort text-gray-300"></i>
    @endif
</span>
