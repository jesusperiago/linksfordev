<?php

namespace App\Livewire\Links;

use App\Models\Link;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
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

    public function render(): View
    {
        return view('livewire.links.index');
    }
}
