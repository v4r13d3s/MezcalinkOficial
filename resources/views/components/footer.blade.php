<footer class="bg-neutral-900 text-white">
  <div class="max-w-7xl mx-auto px-6 lg:pl-12 lg:pr-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
      
      <!-- Columna izquierda: enlaces -->
      <div class="space-y-8">
        <div class="space-y-4">
          <h3 class="text-xl font-extrabold tracking-wide">LINKS DE INTERÉS</h3>
          <ul class="space-y-3 text-gray-300">
            <li><a href="#" class="transition hover:text-white">COMUNIDAD</a></li>
            <li><a href="#" class="transition hover:text-white">¿QUIÉNES SOMOS?</a></li>
            <li><a href="#" class="transition hover:text-white">PREGUNTAS FRECUENTES</a></li>
          </ul>
        </div>
        <div class="space-y-4">
          <h3 class="text-xl font-extrabold tracking-wide">POLÍTICAS</h3>
          <ul class="space-y-3 text-gray-300">
            <li><a href="#" class="transition hover:text-white">POLÍTICAS DE PRIVACIDAD</a></li>
            <li><a href="#" class="transition hover:text-white">TÉRMINOS Y CONDICIONES</a></li>
          </ul>
        </div>
      </div>

      <!-- Columna central: logo -->
      <div class="flex flex-col items-center justify-start lg:justify-center space-y-4">
        <img src="{{ asset('images/MK_w.svg') }}" alt="MEZCALINK" class="h-16">
      </div>

      <!-- Columna derecha: contacto y redes -->
      <div class="space-y-8 lg:justify-self-end lg:text-right lg:pr-8">
        <div class="space-y-4">
          <h3 class="text-xl font-extrabold tracking-wide">CONTACTO</h3>
          <ul class="space-y-3 text-gray-300">
            <li>
              <a href="tel:+524213234322" class="transition hover:text-white">+52 421 323 4322</a>
            </li>
            <li>
              <a href="mailto:mezcalink@mezcal.com" class="transition hover:text-white">MEZCALINK@MEZCAL.COM</a>
            </li>
          </ul>
        </div>
        <div class="space-y-4">
          <h3 class="text-xl font-extrabold tracking-wide">SÍGUENOS EN:</h3>
          <div class="flex items-center gap-5">
            <a href="#" class="transition hover:opacity-100 opacity-90"><img src="{{ asset('images/icons/Facebook.svg') }}" alt="Facebook" class="h-7 w-7"></a>
            <a href="#" class="transition hover:opacity-100 opacity-90"><img src="{{ asset('images/icons/Instagram.svg') }}" alt="Instagram" class="h-7 w-7"></a>
            <a href="#" class="transition hover:opacity-100 opacity-90"><img src="{{ asset('images/icons/Twitter.svg') }}" alt="Twitter" class="h-7 w-7"></a>
            <a href="#" class="transition hover:opacity-100 opacity-90"><img src="{{ asset('images/icons/TikTok.svg') }}" alt="TikTok" class="h-7 w-7"></a>
          </div>
        </div>
      </div>
    </div>

    <!-- Barra inferior -->
    <div class="mt-12 border-t border-white/10 pt-6 flex items-center justify-between text-sm text-gray-400">
      <p>COPYRIGHT © 2025, MEZCALINK, VERACRUZ MÉXICO</p>
      <img src="{{ asset('images/MK_horizontal_w.svg') }}" alt="MEZCALINK" class="h-6">
    </div>
  </div>
</footer>