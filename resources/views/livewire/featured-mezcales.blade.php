<div>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="mb-8">
            <h2 class="text-4xl font-semibold tracking-tight text-center">Mezcales destacados</h2>
        </header>

        <!-- Filtros aplicados -->
        @if(!empty($filtrosAplicados) || $precioMin > 0 || $precioMax < 5000)
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-gray-700">FILTRAR POR:</h3>
                    <button wire:click="clearAllFilters" 
                        class="text-xs text-red-600 hover:text-red-800 font-medium">
                        Limpiar filtros
                    </button>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($filtrosAplicados as $filtro)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                            {{ strtoupper($filtro['nombre']) }}
                            <button wire:click="removeFilter('{{ $filtro['type'] }}', {{ $filtro['id'] }})" 
                                class="ml-2 text-gray-500 hover:text-gray-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </span>
                    @endforeach
                    
                    @if($precioMin > 0 || $precioMax < 5000)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                            PRECIO: ${{ number_format($precioMin) }} - ${{ number_format($precioMax) }}
                            <button wire:click="removeFilter('precio', null)" 
                                class="ml-2 text-gray-500 hover:text-gray-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar filtros dinámicos -->
            <aside class="lg:col-span-1 space-y-6">
                <div class="border rounded-md p-4 space-y-6">
                    <h3 class="font-medium">Filtrar por</h3>
                    
                    <!-- Filtro de Precio -->
					<div class="space-y-3">
						<div class="group">
							<button type="button" wire:click="toggleSection('precio')" class="flex items-center justify-between w-full text-left cursor-pointer select-none">
								<span class="font-semibold text-black">PRECIO</span>
								<svg class="w-4 h-4 transform transition-transform {{ $isPrecioOpen ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
								</svg>
							</button>
							<div class="mt-3 overflow-hidden transition-all duration-300 {{ $isPrecioOpen ? 'max-h-[1000px] opacity-100' : 'max-h-0 opacity-0' }}">
                                <div class="px-3 py-2">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <input type="range" 
                                            wire:model.live.debounce.300ms="precioMin"
                                            min="0" 
                                            max="5000" 
                                            step="100"
                                            class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                    </div>
                                    <div class="flex items-center space-x-3 mb-3">
                                        <input type="range" 
                                            wire:model.live.debounce.300ms="precioMax"
                                            min="0" 
                                            max="5000" 
                                            step="100"
                                            class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>${{ number_format($precioMin) }}</span>
                                        <span>${{ number_format($precioMax) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtro de Tipos de Maduración -->
					<div class="space-y-3">
						<div class="group">
							<button type="button" wire:click="toggleSection('maduracion')" class="flex items-center justify-between w-full text-left cursor-pointer select-none">
								<span class="font-semibold text-black">TIPOS DE MADURACIÓN</span>
								<svg class="w-4 h-4 transform transition-transform {{ $isMaduracionOpen ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
								</svg>
							</button>
                            <div class="mt-3 overflow-hidden transition-all duration-300 {{ $isMaduracionOpen ? 'max-h-[1000px] opacity-100' : 'max-h-0 opacity-0' }} space-y-0.5" id="maduracion-filter">
                                @foreach($tiposMaduracion as $tipo)
                                    <label class="flex items-center gap-3 py-1 cursor-pointer">
                                        <input type="checkbox" 
                                            wire:model.live="selectedTiposMaduracion"
                                            value="{{ $tipo->id }}"
                                            class="w-4 h-4 accent-black text-black bg-white border-2 border-gray-300 rounded focus:ring-2 focus:ring-black">
                                        <span class="text-sm text-gray-950">{{ $tipo->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Filtro de Tipos de Elaboración -->
					<div class="space-y-3">
						<div class="group">
							<button type="button" wire:click="toggleSection('elaboracion')" class="flex items-center justify-between w-full text-left cursor-pointer select-none">
								<span class="font-semibold text-black">TIPOS DE ELABORACIÓN</span>
								<svg class="w-4 h-4 transform transition-transform {{ $isElaboracionOpen ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
								</svg>
							</button>
                            <div id="elaboracion-filter" class="mt-3 overflow-hidden transition-all duration-300 {{ $isElaboracionOpen ? 'max-h-[1000px] opacity-100' : 'max-h-0 opacity-0' }} space-y-0.5">
                                @foreach($tiposElaboracion as $tipo)
                                    <label class="flex items-center gap-3 py-1 cursor-pointer">
                                        <input type="checkbox" 
                                            wire:model.live="selectedTiposElaboracion"
                                            value="{{ $tipo->id }}"
                                            class="w-4 h-4 accent-black text-black bg-white border-2 border-gray-300 rounded focus:ring-2 focus:ring-black">
                                        <span class="text-sm text-gray-950">{{ $tipo->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Filtro de Regiones -->
					<div class="space-y-3">
						<div class="group">
							<button type="button" wire:click="toggleSection('region')" class="flex items-center justify-between w-full text-left cursor-pointer select-none">
								<span class="font-semibold text-black">REGIÓN DE ORIGEN</span>
								<svg class="w-4 h-4 transform transition-transform {{ $isRegionOpen ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
								</svg>
							</button>
                            <div id="region-filter" class="mt-3 overflow-hidden transition-all duration-300 {{ $isRegionOpen ? 'max-h-[1000px] opacity-100' : 'max-h-0 opacity-0' }} space-y-0.5">
                                @foreach($regiones as $region)
                                    <label class="flex items-center gap-3 py-1 cursor-pointer">
                                        <input type="checkbox" 
                                            wire:model.live="selectedRegiones"
                                            value="{{ $region->id }}"
                                            class="w-4 h-4 accent-black text-black bg-white border-2 border-gray-300 rounded focus:ring-2 focus:ring-black">
                                        <span class="text-sm text-gray-950">{{ $region->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Filtro de Categorías -->
					<div class="space-y-3">
						<div class="group">
							<button type="button" wire:click="toggleSection('categoria')" class="flex items-center justify-between w-full text-left cursor-pointer select-none">
								<span class="font-semibold text-black">CATEGORÍAS</span>
								<svg class="w-4 h-4 transform transition-transform {{ $isCategoriaOpen ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
								</svg>
							</button>
                            <div id="categoria-filter" class="mt-3 overflow-hidden transition-all duration-300 {{ $isCategoriaOpen ? 'max-h-[1000px] opacity-100' : 'max-h-0 opacity-0' }} space-y-0.5">
                                @foreach($categorias as $categoria)
                                    <label class="flex items-center gap-3 py-1 cursor-pointer">
                                        <input type="checkbox" 
                                            wire:model.live="selectedCategorias"
                                            value="{{ $categoria->id }}"
                                            class="w-4 h-4 accent-black text-black bg-white border-2 border-gray-300 rounded focus:ring-2 focus:ring-black">
                                        <span class="text-sm text-gray-950">{{ $categoria->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Filtro de Tipos de Agave -->
					<div class="space-y-3">
						<div class="group">
							<button type="button" wire:click="toggleSection('agave')" class="flex items-center justify-between w-full text-left cursor-pointer select-none">
								<span class="font-semibold text-black">TIPO DE AGAVE</span>
								<svg class="w-4 h-4 transform transition-transform {{ $isAgaveOpen ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
								</svg>
							</button>
                            <div id="agave-filter" class="mt-3 overflow-hidden transition-all duration-300 {{ $isAgaveOpen ? 'max-h-[1000px] opacity-100' : 'max-h-0 opacity-0' }} space-y-0.5">
                                @foreach($tiposAgave as $agave)
                                    <label class="flex items-center gap-3 py-1 cursor-pointer">
                                        <input type="checkbox" 
                                            wire:model.live="selectedTiposAgave"
                                            value="{{ $agave->id }}"
                                            class="w-4 h-4 accent-black text-black bg-white border-2 border-gray-300 rounded focus:ring-2 focus:ring-black">
                                        <span class="text-sm text-gray-950">{{ $agave->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Listado dinámico de mezcales -->
            <div class="lg:col-span-3 space-y-6">
                @forelse($mezcales as $mezcal)
                    <article
                        class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 grid grid-cols-12 gap-6 items-center hover:shadow-md transition-shadow">
                        <!-- Imagen del producto -->
                        <div class="col-span-3">
                            <div
                                class="w-full aspect-[3/4] bg-gray-50 rounded-lg flex items-center justify-center overflow-hidden">
                                @php($image = $mezcal->images->first())
                                @if ($image)
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $image->alt ?? $mezcal->nombre }}"
                                        class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full bg-gradient-to-b from-gray-100 to-gray-200 rounded-lg"></div>
                                @endif
                            </div>
                        </div>

                        <!-- Información del producto -->
                        <div class="col-span-6 space-y-3">
                            <!-- Ubicación -->
                            <div class="flex items-center text-sm text-red-500">
                                <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                </svg>
                                <span
                                    class="uppercase font-medium">{{ $mezcal->region?->nombre ?? 'SANTIAGO MATATLÁN, OAXACA, MÉXICO' }}</span>
                            </div>

                            <!-- Nombre del producto -->
                            <h3 class="text-xl font-bold text-gray-900 leading-tight">
                                {{ $mezcal->nombre }}
                            </h3>

                            <!-- Precio regular -->
                            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">
                                Precio regular
                            </p>

                            <!-- Precio -->
                            <div class="text-3xl font-black text-gray-900">
                                ${{ number_format($mezcal->precio_regular, 2) }}
                            </div>
                        </div>

                        <!-- Precio y calificación -->
                        <div class="col-span-3 text-right space-y-3">
                            <!-- Calificación -->
                            <div class="flex flex-col items-center">
                                <div class="text-4xl font-black text-gray-900 mb-1">
                                    {{ $mezcal->calificacion ?? '5' }}
                                </div>
                                <div class="flex items-center mb-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= ($mezcal->calificacion ?? 5) ? 'text-yellow-400' : 'text-gray-200' }} fill-current"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-xs text-gray-500">
                                    {{ $mezcal->total_valoraciones ?? '120' }} VALORACIONES
                                </p>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-lg mb-2">No hay mezcales disponibles</div>
                        <p class="text-sm text-gray-500">Intenta ajustar los filtros de búsqueda</p>
                    </div>
                @endforelse

                <!-- Paginación personalizada -->
                @if($mezcales->hasPages())
                    <div class="flex items-center justify-center space-x-2 mt-8 pt-6 border-t border-gray-200">
                        {{-- Botón Previous/Back --}}
                        @if ($mezcales->onFirstPage())
                            <span class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Back
                            </span>
                        @else
                            <button wire:click="previousPage" wire:loading.attr="disabled" 
                                class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Back
                            </button>
                        @endif

                        {{-- Números de página --}}
                        @foreach ($mezcales->getUrlRange(1, $mezcales->lastPage()) as $page => $url)
                            @if ($page == $mezcales->currentPage())
                                <span class="px-4 py-2 text-sm font-bold text-white bg-gray-900 rounded-md">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach

                        {{-- Mostrar "..." si hay muchas páginas --}}
                        @if($mezcales->lastPage() > 5 && $mezcales->currentPage() < $mezcales->lastPage() - 2)
                            <span class="px-2 py-2 text-sm text-gray-500">...</span>
                        @endif

                        {{-- Botón Next --}}
                        @if ($mezcales->hasMorePages())
                            <button wire:click="nextPage" wire:loading.attr="disabled"
                                class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                Next
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        @else
                            <span class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">
                                Next
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif

                        {{-- Información de página --}}
                        <div class="flex items-center ml-4">
                            <span class="text-sm text-gray-500">Page</span>
                            <input type="number" 
                                wire:model.defer="page"
                                wire:keydown.enter="gotoPage($event.target.value)"
                                min="1" 
                                max="{{ $mezcales->lastPage() }}" 
                                value="{{ $mezcales->currentPage() }}"
                                class="mx-2 w-16 px-2 py-1 text-sm border border-gray-300 rounded text-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button wire:click="gotoPage($refs.pageInput.value)" 
                                class="px-3 py-1 text-sm font-medium text-white bg-gray-900 rounded hover:bg-gray-800 transition-colors duration-200">
                                Go
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    
</div>