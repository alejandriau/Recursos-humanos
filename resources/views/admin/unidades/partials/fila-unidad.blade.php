<!-- Denominación -->
<td class="px-6 py-4 whitespace-nowrap">
    <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-building text-blue-600"></i>
        </div>
        <div class="ml-4">
            <div class="text-sm font-medium text-gray-900">
                <a href="{{ route('unidades.show', $unidad) }}"
                   class="hover:text-blue-600 transition-colors"
                   title="{{ $unidad->denominacion }}">
                    {{ Str::limit($unidad->denominacion, 50) }}
                </a>
            </div>
            @if($unidad->sigla)
            <div class="text-sm text-gray-500">{{ $unidad->sigla }}</div>
            @endif
            @if($unidad->padre)
            <div class="text-xs text-gray-400 mt-1">
                <i class="fas fa-level-up-alt rotate-90 mr-1"></i>
                {{ Str::limit($unidad->padre->denominacion, 30) }}
            </div>
            @endif
        </div>
    </div>
</td>

<!-- Tipo -->
<td class="px-6 py-4 whitespace-nowrap">
    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
        @switch($unidad->tipo)
            @case('SECRETARIA') bg-purple-100 text-purple-800 @break
            @case('SERVICIO') bg-indigo-100 text-indigo-800 @break
            @case('DIRECCION') bg-blue-100 text-blue-800 @break
            @case('UNIDAD') bg-green-100 text-green-800 @break
            @case('AREA') bg-yellow-100 text-yellow-800 @break
            @case('PROGRAMA') bg-pink-100 text-pink-800 @break
            @case('PROYECTO') bg-orange-100 text-orange-800 @break
            @default bg-gray-100 text-gray-800
        @endswitch">
        {{ $unidad->tipo }}
    </span>
</td>

<!-- Código -->
<td class="px-6 py-4 whitespace-nowrap">
    <div class="text-sm text-gray-900 font-mono">
        {{ $unidad->codigo ?? 'N/A' }}
    </div>
</td>

<!-- Jefe -->
<td class="px-6 py-4 whitespace-nowrap">
    @if($unidad->jefe)
        <div class="flex items-center">
            <div class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-green-600 text-xs"></i>
            </div>
            <div class="ml-3">
                <div class="text-sm font-medium text-gray-900">
                    {{ $unidad->jefe->denominacion }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ $unidad->jefe->nivelJerarquico }}
                </div>
            </div>
        </div>
    @else
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
            <i class="fas fa-user-slash mr-1"></i>
            Sin asignar
        </span>
    @endif
</td>

<!-- Subunidades -->
<td class="px-6 py-4 whitespace-nowrap">
    @if(($unidad->hijos_count ?? 0) > 0)
        <a href="{{ route('unidades.index', ['idPadre' => $unidad->id]) }}"
           class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors"
           title="Ver subunidades">
            <i class="fas fa-sitemap mr-1"></i>
            {{ $unidad->hijos_count ?? 0 }}
        </a>
    @else
        <span class="text-sm text-gray-400">-</span>
    @endif
</td>

<!-- Estado -->
<td class="px-6 py-4 whitespace-nowrap">
    @if($unidad->esActivo)
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <i class="fas fa-check-circle mr-1"></i>
            Activo
        </span>
    @else
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
            <i class="fas fa-pause-circle mr-1"></i>
            Inactivo
        </span>
    @endif
</td>

<!-- Acciones -->
<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
    <div class="flex space-x-2" x-show="showActions || window.innerWidth < 768">
        <!-- Ver -->
        <a href="{{ route('unidades.show', $unidad) }}"
           class="text-blue-600 hover:text-blue-900 transition-colors p-1 rounded hover:bg-blue-50"
           title="Ver detalles">
            <i class="fas fa-eye w-4 h-4"></i>
        </a>

        <!-- Editar -->
        <a href="{{ route('unidades.edit', $unidad) }}"
           class="text-green-600 hover:text-green-900 transition-colors p-1 rounded hover:bg-green-50"
           title="Editar">
            <i class="fas fa-edit w-4 h-4"></i>
        </a>

        <!-- Estado -->
        @if($unidad->esActivo)
        <form action="{{ route('unidades.desactivar', $unidad) }}" method="POST" class="inline"
              data-confirm="¿Está seguro de desactivar esta unidad?">
            @csrf
            @method('POST')
            <button type="submit"
                    class="text-yellow-600 hover:text-yellow-900 transition-colors p-1 rounded hover:bg-yellow-50"
                    title="Desactivar">
                <i class="fas fa-pause w-4 h-4"></i>
            </button>
        </form>
        @else
        <form action="{{ route('unidades.reactivar', $unidad) }}" method="POST" class="inline"
              data-confirm="¿Está seguro de reactivar esta unidad?">
            @csrf
            @method('POST')
            <button type="submit"
                    class="text-green-600 hover:text-green-900 transition-colors p-1 rounded hover:bg-green-50"
                    title="Reactivar">
                <i class="fas fa-play w-4 h-4"></i>
            </button>
        </form>
        @endif

        <!-- Eliminar -->
        <form action="{{ route('unidades.destroy', $unidad) }}" method="POST" class="inline"
              data-confirm="¿Está seguro de eliminar esta unidad? Esta acción no se puede deshacer.">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="text-red-600 hover:text-red-900 transition-colors p-1 rounded hover:bg-red-50"
                    title="Eliminar">
                <i class="fas fa-trash w-4 h-4"></i>
            </button>
        </form>
    </div>

    <!-- Indicador de acciones en hover (solo desktop) -->
    <div x-show="!showActions && window.innerWidth >= 768" class="text-gray-400 text-xs">
        Pase el cursor
    </div>
</td>
