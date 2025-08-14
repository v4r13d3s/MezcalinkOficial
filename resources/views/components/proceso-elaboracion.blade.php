{{-- resources/views/components/proceso-elaboracion-section.blade.php --}}
<section class="bg-gradient-to-r from-orange-500 to-orange-600 py-16 lg:py-24" id="proceso-elaboracion-section">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Logo MEZCALNK --}}
        <div class="mb-8 lg:mb-12" data-animate="fadeInUp">
            <img src="{{ asset('images/MK_k_horizontal.svg') }}" class="h-8 opacity-90 ml-4">
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            
            {{-- Imagen del proceso --}}
			<div class="order-2 lg:order-1" data-animate="fadeInLeft">
				<div class="relative">
					{{-- Cuadro decorativo detrás, desplazado para crear cruce --}}
					<div class="absolute inset-0 -translate-x-6 translate-y-6 sm:-translate-x-8 sm:translate-y-8 bg-orange-200 rounded-3xl z-0"></div>

					{{-- Imagen principal ligeramente rotada para efecto cruzado --}}
					<div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl transform -rotate-2">
						<img src="{{ asset('images/corazon-maguey.jpg') }}" 
							alt="Proceso de elaboración del mezcal" 
							class="w-full h-64 sm:h-80 lg:h-96 object-cover">
					</div>

					{{-- Decoraciones circulares detrás --}}
					<div class="absolute -top-4 -left-4 w-16 h-16 sm:w-20 sm:h-20 bg-orange-300 rounded-full opacity-50 -z-10"></div>
					<div class="absolute -bottom-4 -right-4 w-12 h-12 sm:w-16 sm:h-16 bg-orange-300 rounded-full opacity-30 -z-10"></div>
				</div>
			</div>

            {{-- Contenido de texto --}}
            <div class="order-1 lg:order-2 text-center lg:text-left" data-animate="fadeInRight">
                
                {{-- Título principal --}}
                <div class="mb-6 lg:mb-8">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold text-white leading-tight">
                        APRENDE DEL PROCESO<br class="hidden sm:block">
                        DE <span class="text-orange-200">ELABORACIÓN</span>
                    </h2>
                </div>

                {{-- Subtítulo --}}
                <div class="mb-8 lg:mb-12">
                    <p class="text-lg sm:text-xl lg:text-2xl text-white font-medium leading-relaxed">
                        Y SUS MÉTODOS <span class="font-bold text-orange-200">TRADICIONALES</span> Y<br class="hidden sm:block">
                        <span class="font-bold text-orange-200">ANCESTRALES</span>
                    </p>
                </div>

                {{-- Botón CTA --}}
                <div>
                    <a href="#" 
                       class="inline-block bg-white text-orange-600 font-bold text-lg sm:text-xl px-8 sm:px-12 py-4 sm:py-5 
                              rounded-full hover:bg-orange-50 transform hover:scale-105 transition-all duration-300 
                              shadow-lg hover:shadow-2xl uppercase tracking-wide">
                        VER
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>