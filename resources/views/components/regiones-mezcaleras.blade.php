{{-- resources/views/components/regiones-mezcaleras.blade.php --}}
<section class="relative bg-orange-600 py-16 lg:py-0 overflow-hidden lg:h-screen lg:flex lg:flex-col lg:justify-center" id="regiones-mezcaleras-section">
	<div class="container mx-auto px-4 sm:px-6 lg:px-24">
		{{-- Título --}}
		<div class="text-center text-white mb-6 lg:mb-6" data-animate="fadeInUp">
			<h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-wide uppercase">
				REGIONES MEZCALERAS
			</h2>
			<div class="w-40 sm:w-48 lg:w-56 h-1 bg-white mx-auto mt-3 rounded-full"></div>
		</div>

		{{-- Tarjeta con mapa --}}
		<div class="mb-6 lg:mb-6" data-animate="fadeInUp" data-delay="0.1">
			<div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-black/10 overflow-hidden">
				<img src="{{ asset('images/mapa.png') }}" 
					 alt="Mapa regiones mezcaleras" 
					 class="w-full h-56 sm:h-72 lg:h-[340px] object-contain p-4 sm:p-6 lg:p-8 bg-white">
			</div>
		</div>

		{{-- Descripción --}}
		<div class="text-center text-white max-w-4xl mx-auto mb-6 lg:mb-6" data-animate="fadeInUp" data-delay="0.2">
			<p class="text-base sm:text-lg lg:text-lg leading-relaxed">
				CONOCE LOS ESTADOS CON <span class="font-extrabold">CERTIFICACIÓN “DOM”</span> (DENOMINACIÓN DE ORIGEN MEZCAL), Y EN DONDE PUEDES ADQUIRIRLOS
			</p>
		</div>

		{{-- CTA --}}
		<div class="text-center" data-animate="fadeInUp" data-delay="0.3">
			<a href="#" class="inline-block bg-white text-gray-900 font-bold text-lg sm:text-xl px-10 sm:px-12 py-4 sm:py-5 rounded-2xl shadow-lg hover:shadow-2xl hover:bg-gray-100 transform hover:scale-105 transition-all duration-300">
				CONOCER
			</a>
		</div>
	</div>

	{{-- Logo inferior centrado (puedes reemplazar la imagen) --}}
	<div class="absolute left-[95%] -translate-x-1/2 bottom-4 lg:bottom-6" data-animate="fadeIn" data-delay="0.5">
		<img src="{{ asset('images/MK_icon_w.svg') }}" alt="MEZCALNK" class="h-12 sm:h-10 md:h-14 lg:h-12">
	</div>
</section>