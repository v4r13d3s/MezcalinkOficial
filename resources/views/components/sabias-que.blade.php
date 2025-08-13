<section class="relative min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat" 
         style="background-image: url('{{ asset('images/banner2.jpeg') }}');"
         id="sabias-que-section">
    
    {{-- Overlay oscuro para mejor legibilidad --}}
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    
    {{-- Contenido principal --}}
    <div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        
        {{-- Título principal --}}
        <div class="mb-8" data-animate="fadeInUp">
            <h2 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold tracking-wide mb-4">
                SABIAS QUE...
            </h2>
            {{-- Línea decorativa --}}
            <div class="w-32 sm:w-40 lg:w-48 h-1 bg-white mx-auto"></div>
        </div>
        
        {{-- Texto descriptivo --}}
        <div class="mb-12" data-animate="fadeInUp" data-delay="0.2">
            <p class="text-lg sm:text-xl lg:text-2xl xl:text-3xl leading-relaxed font-light max-w-5xl mx-auto">
                A LO LARGO DE LOS SIGLOS, EL PROCESO DE PRODUCCIÓN DEL MEZCAL<br class="hidden sm:block">
                SE HA <span class="font-semibold">PERFECCIONADO</span>, PERO SE HA MANTENIDO ARRAIGADO EN LAS<br class="hidden sm:block">
                <span class="font-semibold">TRADICIONES ANCESTRALES.</span>
            </p>
        </div>
        
        {{-- Botón CTA --}}
        <div class="mb-8" data-animate="fadeInUp" data-delay="0.4">
            <a href="#" 
               class="inline-block bg-white text-black font-bold text-lg sm:text-xl px-8 sm:px-12 py-4 sm:py-5 rounded-full 
                      hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg
                      hover:shadow-2xl uppercase tracking-wide">
                CONOCE SU HISTORIA
            </a>
        </div>
        
        {{-- Logo en esquina inferior derecha --}}
        <div class="absolute bottom-4 right-8 hidden lg:block" data-animate="fadeIn" data-delay="0.6">
            <img src="{{ asset('images/MK_horizontal_w.svg') }}" 
                 alt="MEZCALNK" 
                 class="h-4 xl:h-6 opacity-90">
        </div>
    </div>
    
    {{-- Logo móvil (centrado en la parte inferior) --}}
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 lg:hidden" data-animate="fadeIn" data-delay="0.6">
        <img src="{{ asset('images/MK_horizontal_w.svg') }}" 
             alt="MEZCALNK" 
             class="h-6 opacity-90 mb-4">
    </div>
</section>