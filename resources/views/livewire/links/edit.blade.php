<div class="min-h-screen bg-[#f6f4f0] text-[#18181b] dark:bg-[#0c0e14] dark:text-[#f0eeeb]">
    <main class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <a
            href="{{ route('index') }}"
            class="text-sm font-semibold text-[#1a7a6d] hover:underline dark:text-[#5eead4]">
            ← Volver a los enlaces
        </a>

        <section class="mt-6 rounded-xl border border-[#ddd8cf] bg-white p-6 shadow-sm dark:border-[#262830] dark:bg-[#181a24]">
            <p class="text-xs font-bold uppercase tracking-[0.15em] text-[#1a7a6d] dark:text-[#5eead4]">
                LinksForDev
            </p>

            <h1 class="mt-2 text-2xl font-bold">
                Editar enlace
            </h1>

            <p class="mt-2 text-sm text-[#52525b] dark:text-[#9ca3af]">
                 Actualiza los datos y los tags de este recurso.
            </p>

            <form wire:submit="save" class="mt-6 flex flex-col gap-5">
                <div>
                    <label for="title" class="mb-1 block text-sm font-medium">
                        Título
                    </label>
                    <input
                        id="title"
                        type="text"
                        wire:model="title"
                        required
                        class="w-full rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm outline-none focus:border-[#1a7a6d] focus:ring-2 focus:ring-[#1a7a6d]/20 dark:border-[#262830] dark:bg-[#0c0e14] dark:focus:border-[#5eead4]">
                    @error('title')
                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="url" class="mb-1 block text-sm font-medium">
                        URL
                    </label>
                    <input
                        id="url"
                        type="url"
                        wire:model="url"
                        placeholder="https://example.com/recurso"
                        required
                        class="w-full rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm outline-none focus:border-[#1a7a6d] focus:ring-2 focus:ring-[#1a7a6d]/20 dark:border-[#262830] dark:bg-[#0c0e14] dark:focus:border-[#5eead4]">
                    @error('url')
                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="mb-1 block text-sm font-medium">
                        Descripción
                    </label>
                    <textarea
                        id="description"
                        wire:model="description"
                        rows="4"
                        required
                        class="w-full rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm outline-none focus:border-[#1a7a6d] focus:ring-2 focus:ring-[#1a7a6d]/20 dark:border-[#262830] dark:bg-[#0c0e14] dark:focus:border-[#5eead4]"></textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="author" class="mb-1 block text-sm font-medium">
                        Autor del contenido <span class="font-normal text-[#52525b] dark:text-[#9ca3af]">(opcional)</span>
                    </label>
                    <input
                        id="author"
                        type="text"
                        wire:model="author"
                        class="w-full rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm outline-none focus:border-[#1a7a6d] focus:ring-2 focus:ring-[#1a7a6d]/20 dark:border-[#262830] dark:bg-[#0c0e14] dark:focus:border-[#5eead4]">
                    @error('author')
                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <fieldset>
                    <legend class="text-sm font-medium">
                        Tags <span class="font-normal text-[#52525b] dark:text-[#9ca3af]">(elige uno o más)</span>
                    </legend>

                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($this->tagOptions as $tag)
                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm dark:border-[#262830] dark:bg-[#0c0e14]">
                            <input
                                type="checkbox"
                                wire:model="tagIds"
                                value="{{ $tag->id }}"
                                class="size-4 accent-[#1a7a6d]">
                            <span>{{ $tag->name }}</span>
                        </label>
                        @endforeach
                    </div>

                    @error('tagIds')
                    <p class="mt-2 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </fieldset>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="rounded-lg bg-[#1a7a6d] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#146357] disabled:cursor-wait disabled:opacity-70 dark:bg-[#5eead4] dark:text-[#0c0e14] dark:hover:bg-[#99f6e4]">
                    Guardar cambios
                </button>
            </form>
        </section>
    </main>
</div>