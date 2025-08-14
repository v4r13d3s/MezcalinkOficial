{{-- resources/views/components/comunidad-section.blade.php --}}
<section class="relative bg-[#e8dbb7] py-12 lg:py-0 overflow-hidden lg:h-screen lg:flex lg:flex-col lg:justify-center" id="comunidad-section">
    <div class="container mx-auto px-4 sm:px-6 lg:px-24 pt-6 lg:pt-8 pb-6 lg:pb-8">

        {{-- Header: título + logo a la derecha --}}
        <div class="relative mb-4">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight">
                <span class="text-orange-600">ENTÉRATE DE LAS ÚLTIMAS NOTICIAS DEL </span>
                <span class="text-[#6f7333]">MEZCAL!</span>
            </h2>
            <div class="hidden lg:block absolute right-0 top-0">
                <img src="{{ asset('images/MK_icon_principal.svg') }}" alt="LNK" class="h-16">
            </div>
        </div>

        {{-- Grid principal: tarjeta grande + dos pequeñas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 lg:grid-rows-2 gap-6 lg:h-[320px]">
            {{-- Tarjeta grande (izquierda) --}}
            <article class="relative rounded-3xl overflow-hidden shadow-2xl bg-gray-200 h-64 sm:h-80 lg:h-full lg:col-span-2 lg:row-span-2">
                <img src="{{ asset('images/imagenComunidad1.png') }}" alt="Artículo principal" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/20"></div>

                {{-- Texto sobre imagen --}}
                <div class="absolute bottom-6 left-6 right-20 text-white font-extrabold text-2xl sm:text-3xl leading-tight drop-shadow">
                    ¿CÓMO ELEGIR UN BUEN MEZCAL?
                </div>

                {{-- Iconos sociales a la derecha --}}
                <div class="absolute top-6 right-6 flex flex-col space-y-3">
                    <a href="#" class="w-10 h-10 rounded-full shadow grid place-items-center"><img src="{{ asset('images/icons/Facebook.svg') }}" alt="Facebook" class="w-6 h-6"></a>
                    <a href="#" class="w-10 h-10 rounded-full shadow grid place-items-center"><img src="{{ asset('images/icons/Instagram.svg') }}" alt="Instagram" class="w-6 h-6"></a>
                    <a href="#" class="w-10 h-10 rounded-full shadow grid place-items-center"><img src="{{ asset('images/icons/Shared.svg') }}" alt="Youtube" class="w-6 h-6"></a>
                </div>
            </article>

            {{-- Tarjeta pequeña 1 --}}
            <article class="relative rounded-3xl overflow-hidden shadow-xl bg-gray-200 h-48 sm:h-52 lg:h-full">
                <img src="{{ asset('images/imagenComunidad2.png') }}" alt="Noticia 1" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/25"></div>
                <div class="absolute bottom-4 left-4 right-4 text-white font-bold text-base sm:text-lg leading-snug">
                    SE REALIZA 2DO DÍA DE LA EXPO DEL MEZCAL
                </div>
            </article>

            {{-- Tarjeta pequeña 2 --}}
            <article class="relative rounded-3xl overflow-hidden shadow-xl bg-gray-200 h-48 sm:h-52 lg:h-full">
                <img src="{{ asset('images/imagenComunidad3.png') }}" alt="Noticia 2" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/25"></div>
                <div class="absolute bottom-4 left-4 right-4 text-white font-bold text-base sm:text-lg leading-snug">
                    MEZCAL UNIÓN SE EXPANDE A 11 PAÍSES EN UNA DÉCADA
                </div>
            </article>
        </div>

        {{-- CTA --}}
        <div class="text-center mt-6">
            <a href="#" class="inline-block bg-white text-gray-900 font-bold text-lg sm:text-xl px-10 sm:px-12 py-4 sm:py-5 rounded-full shadow-lg hover:shadow-2xl hover:bg-gray-100 transform hover:scale-105 transition-all duration-300">
                ENTRA A LA COMUNIDAD
            </a>
        </div>
    </div>
</section>