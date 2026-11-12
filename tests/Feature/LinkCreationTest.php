<?php

namespace Tests\Feature;

use App\Livewire\Links\Create;
use App\Models\Link;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LinkCreationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_cannot_access_link_creation_page(): void
    {
        $this->get(route('links.create'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_link_with_multiple_tags(): void
    {
        $user = User::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Laravel Livewire testing')
            ->set('url', 'https://example.com/laravel-testing')
            ->set('description', 'A useful testing resource.')
            ->set('tagIds', $tags->modelKeys())
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('index'));

        $link = Link::query()
            ->where('url', 'https://example.com/laravel-testing')
            ->firstOrFail();

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'user_id' => $user->id,
            'title' => 'Laravel Livewire testing',
        ]);

        foreach ($tags as $tag) {
            $this->assertDatabaseHas('link_tag', [
                'link_id' => $link->id,
                'tag_id' => $tag->id,
            ]);
        }
    }

    public function test_required_link_fields_and_at_least_one_tag_are_enforced(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', '')
            ->set('url', '')
            ->set('description', '')
            ->set('tagIds', [])
            ->call('save')
            ->assertHasErrors([
                'title' => ['required'],
                'url' => ['required'],
                'description' => ['required'],
                'tagIds' => ['required'],
            ]);

        $this->assertDatabaseCount('links', 0);
    }

    public function test_link_creation_rejects_non_http_urls(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Resource with an unsupported scheme')
            ->set('url', 'ftp://example.com/resource')
            ->set('description', 'A useful resource.')
            ->set('tagIds', [$tag->id])
            ->call('save')
            ->assertHasErrors('url');

        $this->assertDatabaseCount('links', 0);
    }

    public function test_link_creation_rejects_tag_ids_that_do_not_exist(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Laravel resource')
            ->set('url', 'https://example.com/laravel')
            ->set('description', 'A useful resource.')
            ->set('tagIds', [999999])
            ->call('save')
            ->assertHasErrors('tagIds.0');

        $this->assertDatabaseCount('links', 0);
    }

    public function test_authenticated_user_can_view_link_creation_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('links.create'))
            ->assertOk()
            ->assertSee('Añadir un enlace');
    }
}
