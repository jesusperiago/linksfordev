<?php

namespace App\Livewire\Links;

use App\Models\Link;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Edit extends Component
{
    public Link $link;

    public string $title = '';

    public string $url = '';

    public string $description = '';

    public string $author = '';

    public array $tagIds = [];

    public function mount(Link $link): void
    {
        $this->authorize('update', $link);

        $this->link = $link;
        $this->title = $link->title;
        $this->url = $link->url;
        $this->description = $link->description;
        $this->author = $link->author ?? '';
        $this->tagIds = $link->tags
            ->pluck('id')
            ->map(static fn (int $tagId): string => (string) $tagId)
            ->all();
    }

    #[Computed]
    public function tagOptions(): Collection
    {
        return Tag::query()->orderBy('name')->get();
    }

    public function save(): void
    {
        $this->authorize('update', $this->link);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url:http,https'],
            'description' => ['required', 'string'],
            'author' => ['nullable', 'string', 'max:255'],
            'tagIds' => ['required', 'array', 'min:1'],
            'tagIds.*' => ['required', 'integer', 'distinct', 'exists:tags,id'],
        ]);

        DB::transaction(function () use ($validated): void {
            $this->link->update([
                'title' => $validated['title'],
                'url' => $validated['url'],
                'description' => $validated['description'],
                'author' => $validated['author'] ?? null,
            ]);

            $this->link->tags()->sync($validated['tagIds']);
        });

        $this->redirect(route('index'));
    }

    public function render(): View
    {
        return view('livewire.links.edit');
    }
}
