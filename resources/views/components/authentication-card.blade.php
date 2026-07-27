<div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-100">
    <div>
        {{ $logo }}
    </div>

    <!-- Tarjeta modificada -->
    <div class="w-full sm:max-w-md mt-6 bg-white shadow-md overflow-hidden sm:rounded-lg flex flex-col" >
        <!-- Contenido principal con padding -->
        <div class="px-6 py-4 flex-1">
            {{ $slot }}
        </div>

        <!-- Imagen al pie, sin padding, ocupa todo el ancho -->
        <div class="w-full">
            <img src="{{ URL::asset('images/pie-logo-i.jpg') }}" alt="" class="w-full block max-h-4 object-cover">
        </div>
    </div>
</div>
