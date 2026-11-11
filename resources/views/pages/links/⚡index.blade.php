<?php

use App\Models\Link;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Tag;

new class extends Component
{

    public string $search = '';
    public string $tagFilter = '';

    public function clearFilters(): void
    {
        $this->search = '';
        $this->tagFilter = '';
    }

    #[Computed]
    public function tagOptions(): Collection
    {
        return Tag::query()->orderBy('name')->get();
    }

    #[Computed]
    public function links(): Collection
    {
        $searchTerm = trim($this->search);

        $query = Link::query()
            ->with(['addedBy', 'tags']);

        if ($searchTerm !== '') {
            $likeTerm = "%{$searchTerm}%";

            $query->where(function (Builder $query) use ($likeTerm): void {
                $query
                    ->where('title', 'like', $likeTerm)
                    ->orWhere('description', 'like', $likeTerm)
                    ->orWhere('author', 'like', $likeTerm);
            });
        }

        if ($this->tagFilter !== '') {
            $tagId = $this->tagFilter;

            $query->whereHas('tags', function (Builder $tagQuery) use ($tagId): void {
                $tagQuery->where('tags.id', $tagId);
            });
        }
        return $query->latest()->get();
    }
};
?>

<div class="min-h-screen bg-[#f6f4f0] text-[#18181b] dark:bg-[#0c0e14] dark:text-[#f0eeeb]">
    <header class="mx-auto max-w-[1800px] px-4 pt-10 pb-7 sm:px-6 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.15em] text-[#1a7a6d] dark:text-[#5eead4]">
            LinksForDev · Recursos de la comunidad
        </p>

        <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">
            Enlaces para developers
        </h1>

        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#52525b] dark:text-[#9ca3af]">
            Artículos, herramientas, vídeos y otros recursos interesantes compartidos por developers.
        </p>
    </header>

    <main class="mx-auto max-w-[1800px] px-4 pb-12 sm:px-6 lg:px-8">
        <section aria-labelledby="recent-links-heading">
            <div class="mb-3 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-[0.6875rem] font-semibold uppercase tracking-[0.15em] text-[#1a7a6d] dark:text-[#5eead4]">
                        Comunidad
                    </p>
                    <h2 id="recent-links-heading" class="mt-1 text-lg font-bold">
                        Enlaces recientes
                    </h2>
                </div>

                <span class="text-[0.8125rem] font-semibold text-[#52525b] dark:text-[#9ca3af]">
                    {{ $this->links->count() }} enlaces
                </span>
            </div>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row">
                <div class="w-full max-w-md">
                    <label for="search-links" class="sr-only">
                        Buscar enlaces
                    </label>

                    <input
                        id="search-links"
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar por título, descripción o autor..."
                        class="w-full rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#1a7a6d] focus:ring-2 focus:ring-[#1a7a6d]/20 dark:border-[#262830] dark:bg-[#181a24] dark:focus:border-[#5eead4] dark:focus:ring-[#5eead4]/20">
                </div>

                <div class="w-full sm:max-w-xs">
                    <label for="tag-filter" class="sr-only">
                        Filtrar por tag
                    </label>

                    <select
                        id="tag-filter"
                        wire:model.live="tagFilter"
                        class="w-full rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#1a7a6d] focus:ring-2 focus:ring-[#1a7a6d]/20 dark:border-[#262830] dark:bg-[#181a24] dark:focus:border-[#5eead4] dark:focus:ring-[#5eead4]/20">
                        <option value="">Todos los tags</option>

                        @foreach ($this->tagOptions as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($this->search !== '' || $this->tagFilter !== '')
                <div class="mb-4 flex justify-end">
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="rounded-lg border border-[#ddd8cf] bg-white px-3 py-2 text-sm font-medium text-[#1a7a6d] transition hover:border-[#1a7a6d] dark:border-[#262830] dark:bg-[#181a24] dark:text-[#5eead4] dark:hover:border-[#5eead4]">
                        Limpiar filtros
                    </button>
                </div>
                @endif
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @forelse ($this->links as $link)
                <article
                    wire:key="link-{{ $link->id }}"
                    class="group flex h-full min-h-0 flex-col rounded-xl border border-[#ddd8cf] bg-white p-4 transition duration-150 hover:border-[#1a7a6d] hover:shadow-[0_4px_16px_-8px_rgba(26,122,109,0.45)] dark:border-[#262830] dark:bg-[#181a24] dark:hover:border-[#5eead4]">
                    <div class="mb-3 flex flex-wrap items-center gap-1.5">
                        @foreach ($link->tags as $tag)
                        <span class="rounded-full border border-[#1a7a6d]/25 bg-[#1a7a6d]/10 px-2 py-0.5 text-[0.6875rem] font-medium text-[#1a7a6d] dark:border-[#5eead4]/30 dark:bg-[#5eead4]/10 dark:text-[#5eead4]">
                            {{ $tag->name }}
                        </span>
                        @endforeach
                    </div>

                    <h3 class="line-clamp-2 wrap-break-words text-[0.9375rem] font-semibold leading-tight">
                        <a
                            href="{{ $link->url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="outline-none transition-colors group-hover:text-[#1a7a6d] focus-visible:underline dark:group-hover:text-[#5eead4]">
                            {{ $link->title }}
                        </a>
                    </h3>

                    <p class="mt-1.5 line-clamp-3 wrap-break-words text-[0.8125rem] leading-relaxed text-[#52525b] dark:text-[#9ca3af]">
                        {{ $link->description }}
                    </p>

                    <div class="mt-auto flex flex-col gap-1.5 pt-3 text-xs text-[#52525b] dark:text-[#9ca3af]">
                        <div class="flex items-center justify-between gap-2">
                            <span class="min-w-0 truncate">
                                Añadido por {{ $link->addedBy->name }}
                            </span>

                            <time datetime="{{ $link->created_at->toDateString() }}" class="shrink-0 tabular-nums">
                                {{ $link->created_at->format('d/m/Y') }}
                            </time>
                        </div>

                        @if ($link->author)
                        <p class="truncate">
                            Autor: {{ $link->author }}
                        </p>
                        @endif
                    </div>
                </article>
                @empty
                <p class="col-span-full rounded-xl border border-dashed border-[#ddd8cf] bg-white p-8 text-center text-sm text-[#52525b] dark:border-[#262830] dark:bg-[#181a24] dark:text-[#9ca3af]">
                    Todavía no hay enlaces. ¡Sé el primero en compartir uno!
                </p>
                @endforelse
            </div>
        </section>
    </main>
</div>