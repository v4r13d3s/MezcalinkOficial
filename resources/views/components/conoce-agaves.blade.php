{{-- resources/views/components/conoce-agaves.blade.php --}}
<section class="relative bg-[#6f7333] py-16 lg:py-0 overflow-hidden lg:h-screen lg:flex lg:items-center" id="conoce-agaves-section">
	<div class="container mx-auto px-4 sm:px-6 lg:px-24">
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">

			{{-- Columna de texto --}}
			<div class="text-center lg:text-left" data-animate="fadeInLeft">
				<h2 class="text-3xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
					CONOCE MÁS SOBRE<br class="hidden sm:block"> LOS AGAVES
				</h2>
				<p class="text-lg sm:text-xl lg:text-2xl text-white font-medium mb-10">
					Y TODA SU <span class="font-extrabold text-[#e8dbb7]">VARIEDAD</span>
				</p>
				<a href="#" class="inline-block bg-white text-gray-900 font-bold text-lg sm:text-xl px-10 sm:px-12 py-4 sm:py-5 rounded-2xl shadow-lg hover:shadow-2xl hover:bg-gray-100 transform hover:scale-105 transition-all duration-300">
					VER
				</a>
			</div>

			{{-- Columna de imagen con cruce --}}
			<div class="order-first lg:order-none" data-animate="fadeInRight">
				<div class="relative">
					{{-- Cuadro de fondo desplazado (color arena) --}}
					<div class="absolute inset-0 translate-x-8 translate-y-8 sm:translate-x-10 sm:translate-y-10 bg-[#e8dbb7] rounded-3xl z-0"></div>

					{{-- Imagen principal ligeramente rotada para efecto cruzado --}}
					<div class="relative z-10 rounded-3xl overflow-hidden shadow-2xl transform -rotate-1">
						<img src="{{ asset('images/señor_agave.png') }}" alt="Agaves" class="w-full h-64 sm:h-80 lg:h-96 object-cover">
					</div>
				</div>
			</div>

		</div>
	</div>

	{{-- Logo esquina inferior izquierda (puedes reemplazar la imagen) --}}
	<div class="absolute left-8 bottom-8" data-animate="fadeIn" data-delay="0.6">
		<img src="{{ asset('images/MK_icon_w.svg') }}" alt="LNK" class="h-12 opacity-90">
	</div>
</section>