<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <header class="mb-8">
        <h2 class="text-4xl font-semibold tracking-tight text-center">Mezcales destacados</h2>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar filtros (estático, sin funcionalidad por ahora) -->
        <aside class="lg:col-span-1 space-y-6">
            <div class="border rounded-md p-4">
                <h3 class="font-medium mb-3">Filtrar por</h3>
                <div class="space-y-4 text-sm text-gray-600">
                    <div>
                        <p class="font-semibold text-gray-800 mb-2">Precio</p>
                        <div class="h-2 bg-gray-200 rounded"></div>
                        <div class="flex justify-between mt-2">
                            <span>$0</span>
                            <span>$—</span>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-2">Tipos de mezcal</p>
                        <ul class="space-y-1">
                            <li class="flex items-center gap-2"><span
                                    class="w-4 h-4 border rounded inline-block"></span><span>Joven</span></li>
                            <li class="flex items-center gap-2"><span
                                    class="w-4 h-4 border rounded inline-block"></span><span>Reposado</span></li>
                            <li class="flex items-center gap-2"><span
                                    class="w-4 h-4 border rounded inline-block"></span><span>Añejo</span></li>
                            <li class="flex items-center gap-2"><span
                                    class="w-4 h-4 border rounded inline-block"></span><span>Abocado</span></li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-2">Tipo de agave</p>
                        <ul class="space-y-1">
                            <li class="flex items-center gap-2"><span
                                    class="w-4 h-4 border rounded inline-block"></span><span>Espadín</span></li>
                            <li class="flex items-center gap-2"><span
                                    class="w-4 h-4 border rounded inline-block"></span><span>Tobalá</span></li>
                            <li class="flex items-center gap-2"><span
                                    class="w-4 h-4 border rounded inline-block"></span><span>Jabalí</span></li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-2">Región de origen</p>
                        <ul class="space-y-1">
                            <li class="flex items-center gap-2"><span
                                    class="w-4 h-4 border rounded inline-block"></span><span>Oaxaca</span></li>
                            <li class="flex items-center gap-2"><span
                                    class="w-4 h-4 border rounded inline-block"></span><span>Durango</span></li>
                            <li class="flex items-center gap-2"><span
                                    class="w-4 h-4 border rounded inline-block"></span><span>Guerrero</span></li>
                        </ul>
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

                        <!-- Precio -->
                        <div class="text-3xl font-black text-gray-900">
                            ${{ number_format($mezcal->precio_regular, 2) }}
                        </div>

                        </p>
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
        </div>
    </div>
</section>
