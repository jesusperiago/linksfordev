<?php

namespace App\Livewire\Links;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Create extends Component
{
    public string $title = '';

    public string $url = '';

    public string $description = '';

    public string $author = '';

    public array $tagIds = [];

    #[Computed]
    public function tagOptions(): Collection
    {
        return Tag::query()->orderBy('name')->get();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url:http,https'],
            'description' => ['required', 'string'],
            'author' => ['nullable', 'string', 'max:255'],
            'tagIds' => ['required', 'array', 'min:1'],
            'tagIds.*' => ['required', 'integer', 'distinct', 'exists:tags,id'],
        ]);

        $user = Auth::user();

        if (! ($user instanceof User)) {
            abort(403);
        }

        DB::transaction(function () use ($user, $validated): void {
            $link = $user->links()->create([
                'title' => $validated['title'],
                'url' => $validated['url'],
                'description' => $validated['description'],
                'author' => $validated['author'] ?? null,
            ]);

            $link->tags()->sync($validated['tagIds']);
        });

        $this->redirect(route('index'));
    }

    public function render(): View
    {
        return view('livewire.links.create');
    }
}
