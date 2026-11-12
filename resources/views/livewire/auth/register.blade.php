<div class="min-h-screen bg-[#f6f4f0] text-[#18181b] dark:bg-[#0c0e14] dark:text-[#f0eeeb]">
    <main class="mx-auto flex min-h-screen max-w-[1800px] items-center justify-center px-4 py-12">
        <section class="w-full max-w-md rounded-xl border border-[#ddd8cf] bg-white p-6 shadow-sm dark:border-[#262830] dark:bg-[#181a24]">
            <p class="text-xs font-bold uppercase tracking-[0.15em] text-[#1a7a6d] dark:text-[#5eead4]">
                LinksForDev
            </p>

            <h1 class="mt-2 text-2xl font-bold">
                Crear cuenta
            </h1>

            <p class="mt-2 text-sm text-[#52525b] dark:text-[#9ca3af]">
                Regístrate para compartir enlaces con la comunidad.
            </p>

            <form wire:submit="register" class="mt-6 flex flex-col gap-4">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium">
                        Nombre
                    </label>
                    <input
                        id="name"
                        type="text"
                        wire:model="name"
                        autocomplete="name"
                        required
                        class="w-full rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm outline-none focus:border-[#1a7a6d] focus:ring-2 focus:ring-[#1a7a6d]/20 dark:border-[#262830] dark:bg-[#0c0e14] dark:focus:border-[#5eead4]"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium">
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        wire:model="email"
                        autocomplete="email"
                        required
                        class="w-full rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm outline-none focus:border-[#1a7a6d] focus:ring-2 focus:ring-[#1a7a6d]/20 dark:border-[#262830] dark:bg-[#0c0e14] dark:focus:border-[#5eead4]"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium">
                        Contraseña
                    </label>
                    <input
                        id="password"
                        type="password"
                        wire:model="password"
                        autocomplete="new-password"
                        required
                        class="w-full rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm outline-none focus:border-[#1a7a6d] focus:ring-2 focus:ring-[#1a7a6d]/20 dark:border-[#262830] dark:bg-[#0c0e14] dark:focus:border-[#5eead4]"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password-confirmation" class="mb-1 block text-sm font-medium">
                        Confirmar contraseña
                    </label>
                    <input
                        id="password-confirmation"
                        type="password"
                        wire:model="password_confirmation"
                        autocomplete="new-password"
                        required
                        class="w-full rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm outline-none focus:border-[#1a7a6d] focus:ring-2 focus:ring-[#1a7a6d]/20 dark:border-[#262830] dark:bg-[#0c0e14] dark:focus:border-[#5eead4]"
                    >
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="rounded-lg bg-[#1a7a6d] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#146357] disabled:cursor-wait disabled:opacity-70 dark:bg-[#5eead4] dark:text-[#0c0e14] dark:hover:bg-[#99f6e4]"
                >
                    Crear cuenta
                </button>
                            <p class="mt-5 text-sm text-[#52525b] dark:text-[#9ca3af]">
                ¿Ya tienes cuenta?
                <a
                    href="{{ route('login') }}"
                    class="font-semibold text-[#1a7a6d] hover:underline dark:text-[#5eead4]">
                    Inicia sesión
                </a>
            </p>
            </form>
        </section>
    </main>
</div>