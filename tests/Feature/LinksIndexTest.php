<?php

namespace Tests\Feature;

use App\Livewire\Links\Index;
use App\Models\Link;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LinksIndexTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_index_page_loads_successfully(): void
    {
        $response = $this->get(route('index'));

        $response->assertOk();
    }

    public function test_index_displays_a_link(): void
    {
        $link = Link::factory()->create([
            'title' => 'Laravel testing guide',
        ]);

        $response = $this->get(route('index'));

        $response->assertOk();
        $response->assertSee($link->title);
    }

    public function test_search_filters_links_by_title_description_and_author(): void
    {
        $titleMatch = Link::factory()->create([
            'title' => 'needle in the title',
            'description' => 'Unrelated description',
            'author' => 'Other author',
        ]);

        $descriptionMatch = Link::factory()->create([
            'title' => 'Other title',
            'description' => 'needle in the description',
            'author' => 'Other author',
        ]);

        $authorMatch = Link::factory()->create([
            'title' => 'Another title',
            'description' => 'Unrelated description',
            'author' => 'needle author',
        ]);

        $noMatch = Link::factory()->create([
            'title' => 'Vue guide',
            'description' => 'A JavaScript resource',
            'author' => 'Someone else',
        ]);

        Livewire::test(Index::class)
            ->set('search', 'needle')
            ->assertSee($titleMatch->title)
            ->assertSee($descriptionMatch->title)
            ->assertSee($authorMatch->title)
            ->assertDontSee($noMatch->title);
    }

    public function test_tag_filter_only_displays_links_with_selected_tag(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'laravel',
        ]);

        $taggedLink = Link::factory()->create([
            'title' => 'Resource with Laravel tag',
        ]);
        $taggedLink->tags()->attach($tag);

        $untaggedLink = Link::factory()->create([
            'title' => 'Resource without this tag',
        ]);

        Livewire::test(Index::class)
            ->set('tagFilter', (string) $tag->id)
            ->assertSee($taggedLink->title)
            ->assertDontSee($untaggedLink->title);
    }

    public function test_clear_filters_resets_search_and_tag_filter(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'laravel',
        ]);

        $link = Link::factory()->create([
            'title' => 'Laravel resource',
            'description' => 'A useful guide',
            'author' => 'A developer',
        ]);
        $link->tags()->attach($tag);

        Livewire::test(Index::class)
            ->set('search', 'no matching text')
            ->set('tagFilter', (string) $tag->id)
            ->assertSee('Limpiar filtros')
            ->assertDontSee($link->title)
            ->assertSee('No se encontraron enlaces que coincidan con los filtros aplicados.')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('tagFilter', '')
            ->assertSee($link->title);
    }

    public function test_search_and_tag_filter_return_only_links_matching_both(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'laravel',
        ]);

        $matchingLink = Link::factory()->create([
            'title' => 'Laravel testing resource',
            'description' => 'A useful guide',
            'author' => 'A developer',
        ]);
        $matchingLink->tags()->attach($tag);

        $searchOnlyLink = Link::factory()->create([
            'title' => 'Laravel resource without this tag',
            'description' => 'Another guide',
            'author' => 'Another developer',
        ]);

        $tagOnlyLink = Link::factory()->create([
            'title' => 'Testing resource',
            'description' => 'A useful article',
            'author' => 'A writer',
        ]);
        $tagOnlyLink->tags()->attach($tag);

        Livewire::test(Index::class)
            ->set('search', 'Laravel')
            ->set('tagFilter', (string) $tag->id)
            ->assertSee($matchingLink->title)
            ->assertDontSee($searchOnlyLink->title)
            ->assertDontSee($tagOnlyLink->title);
    }

    public function test_index_shows_initial_empty_message_when_no_links_exist(): void
    {
        Livewire::test(Index::class)
            ->assertSee('Todavía no hay enlaces. ¡Sé el primero en compartir uno!');
    }

    public function test_links_show_username_without_exposing_name_or_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Private Full Name',
            'email' => 'private@example.test',
            'username' => 'public_handle',
        ]);

        Link::factory()
            ->for($user, 'addedBy')
            ->create([
                'title' => 'Privacy test resource',
            ]);

        Livewire::test(Index::class)
            ->assertSee('Añadido por @public_handle')
            ->assertDontSee('Private Full Name')
            ->assertDontSee('private@example.test');
    }
}
