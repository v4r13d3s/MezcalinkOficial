<nav class="bg-white shadow px-16 py-4 flex items-center justify-between relative">
    <!-- Logo -->
    <div class="flex items-center space-x-4">
        <img src="{{ asset('images/MK_icon_principal.svg') }}" alt="Mezcalink Logo" class="h-14 w-auto">
    </div>

    <!-- Botón hamburguesa SOLO en móvil -->
    <button class="lg:hidden block" wire:click="toggleMenu">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Centro: Buscador y menú (oculto en móvil) -->
    <div class="flex-col gap-3 w-[700px] hidden lg:flex">
        <label for="Search">
            <div class="relative">
                <input type="text" id="Search"
                    class="mt-0.5 w-full border-round rounded-xl border-gray-300 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-white-900 dark:text-black"
                    placeholder="Buscar mezcales..." />
                <span class="absolute inset-y-0 right-2 grid w-8 place-content-center">
                    <button type="button" aria-label="Submit"
                        class="rounded-full p-1.5 text-black transition-colors hover:bg-gray-100 dark:text-black dark:hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </span>
            </div>
        </label>
        <ul class="flex items-center space-x-6 justify-center gap-2">
            <li>
                <a href="#" class="text-gray-900 hover:font-medium hover:underline hover:decoration-orange-500 hover:decoration-4 hover:underline-offset-8 text-2xl">Mezcales</a>
            </li>
            <li>
                <a href="#" class="text-gray-900 hover:font-medium hover:underline hover:decoration-orange-500 hover:decoration-4 hover:underline-offset-8 text-2xl">Marcas</a>
            </li>
            <li>
                <a href="#" class="text-gray-900 hover:font-medium hover:underline hover:decoration-orange-500 hover:decoration-4 hover:underline-offset-8 text-2xl">Regiones</a>
            </li>
            <li>
                <a href="#" class="text-gray-900 hover:font-medium hover:underline hover:decoration-orange-500 hover:decoration-4 hover:underline-offset-8 text-2xl">Comunidad</a>
            </li>
        </ul>
    </div>

    <!-- Acciones (oculto en móvil) -->
    <div class="flex flex-row gap-3 hidden lg:flex">
        <a href="#" class="group">
            <div class="flex flex-col gap-1 items-center rounded-xl hover:bg-orange-600 w-20 h-20 py-2 group-hover:shadow-[4px_4px_8px_rgba(0,0,0,0.3)]">
                <img src="{{ asset('images/icons/shopping_bag.svg') }}" alt="" class="w-5 h-5 group-hover:brightness-0 group-hover:invert">
                <span class="text-black px-1 text-sm text-center group-hover:text-white">Club de suscripción</span>
            </div>
        </a>
        <a href="#" class="group">
            <div class="flex flex-col gap-1 items-center rounded-xl hover:bg-orange-600 w-20 h-20 py-2 group-hover:shadow-[4px_4px_8px_rgba(0,0,0,0.3)]">
                <img src="{{ asset('images/icons/interface.svg') }}" alt="" class="w-5 h-5 group-hover:brightness-0 group-hover:invert">
                <span class="text-black px-1 text-sm text-center group-hover:text-white">Iniciar sesión</span>
            </div>
        </a>
    </div>

    <!-- Menú móvil (buscador, links y acciones) -->
    @if($menuOpen || $isClosing)
    <div
        class="absolute top-full left-0 w-full bg-white shadow-lg z-50 px-4 py-6 flex flex-col gap-6 lg:hidden
            @if($isClosing) animate-fade-out @else animate-fade-in @endif"
        id="mobileMenu"
    >
        <label for="SearchMobile">
            <div class="relative mb-4">
                <input type="text" id="SearchMobile"
                    class="w-full border-round rounded-xl border-gray-300 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-white-900 dark:text-black"
                    placeholder="Buscar mezcales..." />
                <span class="absolute inset-y-0 right-2 grid w-8 place-content-center">
                    <button type="button" aria-label="Submit"
                        class="rounded-full p-1.5 text-black transition-colors hover:bg-gray-100 dark:text-black dark:hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </span>
            </div>
        </label>
        <ul class="flex flex-col gap-4 items-center">
            <li><a href="#" class="text-gray-900 text-xl">Mezcales</a></li>
            <li><a href="#" class="text-gray-900 text-xl">Marcas</a></li>
            <li><a href="#" class="text-gray-900 text-xl">Regiones</a></li>
            <li><a href="#" class="text-gray-900 text-xl">Comunidad</a></li>
        </ul>
        <div class="flex flex-col gap-3 items-center">
            <a href="#" class="group w-full">
                <div class="flex flex-col gap-1 items-center rounded-xl hover:bg-orange-600 w-full py-2 group-hover:shadow-[4px_4px_8px_rgba(0,0,0,0.3)]">
                    <img src="{{ asset('assets/icons/shopping_bag.svg') }}" alt="" class="w-5 h-5 group-hover:brightness-0 group-hover:invert">
                    <span class="text-black px-1 text-sm text-center group-hover:text-white">Club de suscripción</span>
                </div>
            </a>
            <a href="#" class="group w-full">
                <div class="flex flex-col gap-1 items-center rounded-xl hover:bg-orange-600 w-full py-2 group-hover:shadow-[4px_4px_8px_rgba(0,0,0,0.3)]">
                    <img src="{{ asset('assets/icons/interface.svg') }}" alt="" class="w-5 h-5 group-hover:brightness-0 group-hover:invert">
                    <span class="text-black px-1 text-sm text-center group-hover:text-white">Iniciar sesión</span>
                </div>
            </a>
        </div>
    </div>
    @endif
</nav>

<script>
    window.addEventListener('close-menu', function () {
        setTimeout(function () {
            @this.set('menuOpen', false);
            @this.set('isClosing', false);
        }, 100); // 200ms igual que la animación
    });
</script>