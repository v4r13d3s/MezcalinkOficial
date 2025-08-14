{{-- resources/views/components/conoce-marcas-section.blade.php --}}
<section class="bg-gradient-to-br from-orange-100 via-amber-50 to-orange-200 py-16 lg:py-0 relative overflow-hidden lg:h-screen lg:flex lg:flex-col lg:justify-center" 
         id="conoce-marcas-section">
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- Header con título y logo --}}
        <div class="relative mb-6 lg:mb-0.5">
            
            {{-- Título principal --}}
            <div class="mb-8 lg:mb-0" data-animate="fadeInUp">
                <h2 class="text-3xl sm:text-4xl lg:text-4xl xl:text-4xl font-bold leading-tight text-center">
                    <span class="text-orange-600">CONOCE LAS MARCAS</span><br>
                    <span class="text-orange-400">MÁS RECONOCIDAS</span>
                </h2>
            </div>

            {{-- Logo LNK --}}
            <div class="flex justify-center lg:justify-end lg:absolute lg:right-0 lg:top-0" data-animate="fadeInUp" data-delay="0.2">
                <img src="{{ asset('images/MK_icon_principal.svg') }}" 
                     alt="LNK Logo" 
                     class="h-16 sm:h-20 lg:h-20">
            </div>
            
        </div>

        {{-- Contenedor de imágenes superpuestas --}}
        <div class="relative h-80 sm:h-[420px] lg:h-[420px] mb-1" data-animate="fadeInUp" data-delay="0.4">
            
            {{-- Imagen 1 - Pensador (izquierda, más grande) --}}
            <div class="absolute top-0 left-6 sm:left-10 lg:left-14 w-64 sm:w-80 lg:w-80 h-72 sm:h-96 lg:h-[360px] 
                        bg-green-800 rounded-3xl overflow-hidden shadow-2xl transform -rotate-6 z-30">
                <img src="{{ asset('images/marca1.png') }}" 
                     alt="Mezcal Pensador" 
                     class="w-full h-full object-cover">
            </div>

            {{-- Imagen 2 - 400 Conejos (centro) --}}
            <div class="absolute top-8 sm:top-12 left-1/2 transform -translate-x-1/2 
                        w-48 sm:w-60 lg:w-64 h-56 sm:h-72 lg:h-72 
                        bg-amber-600 rounded-3xl overflow-hidden shadow-2xl rotate-3 z-20">
                <img src="{{ asset('images/marca2.png') }}" 
                     alt="400 Conejos Mezcal" 
                     class="w-full h-full object-cover">
            </div>

            {{-- Imagen 3 - Maya Jules (derecha) --}}
            <div class="absolute top-16 sm:top-20 right-6 sm:right-10 lg:right-14 
                        w-56 sm:w-72 lg:w-72 h-64 sm:h-80 lg:h-80 
                        bg-orange-600 rounded-3xl overflow-hidden shadow-2xl transform rotate-6 z-10">
                <img src="{{ asset('images/marca3.png') }}" 
                     alt="Maya Jules Mezcal" 
                     class="w-full h-full object-cover">
            </div>

            {{-- Formas decorativas de fondo --}}
            <div class="absolute -top-8 -left-8 w-32 h-32 bg-orange-300 rounded-full opacity-20 -z-10"></div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-amber-300 rounded-full opacity-30 -z-10"></div>
            <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-orange-400 rounded-full opacity-15 -z-10"></div>
            
        </div>

        {{-- Botón CTA centrado --}}
        <div class="text-center" data-animate="fadeInUp" data-delay="0.6">
            <a href="#" 
               class="inline-block bg-white text-gray-900 font-bold text-xl sm:text-2xl px-12 sm:px-16 py-5 sm:py-6 
                      rounded-full hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 
                      shadow-lg hover:shadow-2xl uppercase tracking-wide">
                ENTÉRATE!
            </a>
        </div>

    </div>

    {{-- Elementos decorativos adicionales --}}
    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-orange-200 to-transparent rounded-full opacity-30 -z-10"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-amber-200 to-transparent rounded-full opacity-20 -z-10"></div>
    
</section>