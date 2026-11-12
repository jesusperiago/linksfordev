<?php

namespace Tests\Feature;

use App\Livewire\Links\Edit;
use App\Models\Link;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LinkEditingTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_owner_can_edit_link_and_replace_tags(): void
    {
        $owner = User::factory()->create();
        $oldTag = Tag::factory()->create();
        $newTag = Tag::factory()->create();

        $link = Link::factory()
            ->for($owner, 'addedBy')
            ->create([
                'title' => 'Original title',
                'url' => 'https://example.com/original',
                'description' => 'Original description',
                'author' => 'Original author',
            ]);

        $link->tags()->attach($oldTag);

        Livewire::actingAs($owner)
            ->test(Edit::class, ['link' => $link])
            ->assertSet('title', 'Original title')
            ->assertSet('tagIds', [(string) $oldTag->id])
            ->set('title', 'Updated title')
            ->set('url', 'https://example.com/updated')
            ->set('description', 'Updated description')
            ->set('author', 'Updated author')
            ->set('tagIds', [(string) $newTag->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('index'));

        $link->refresh();

        $this->assertSame('Updated title', $link->title);
        $this->assertSame('https://example.com/updated', $link->url);
        $this->assertSame('Updated description', $link->description);
        $this->assertSame('Updated author', $link->author);
        $this->assertSame($owner->id, $link->user_id);

        $this->assertDatabaseHas('link_tag', [
            'link_id' => $link->id,
            'tag_id' => $newTag->id,
        ]);
        $this->assertDatabaseMissing('link_tag', [
            'link_id' => $link->id,
            'tag_id' => $oldTag->id,
        ]);
    }

    public function test_other_user_cannot_open_link_edit_page(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $link = Link::factory()
            ->for($owner, 'addedBy')
            ->create();

        $this->actingAs($otherUser)
            ->get(route('links.edit', $link))
            ->assertForbidden();

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_guest_is_redirected_to_login_from_link_edit_page(): void
    {
        $owner = User::factory()->create();

        $link = Link::factory()
            ->for($owner, 'addedBy')
            ->create();

        $this->get(route('links.edit', $link))
            ->assertRedirect(route('login'));
    }

    public function test_invalid_edit_does_not_change_link_or_tags(): void
    {
        $owner = User::factory()->create();
        $tag = Tag::factory()->create();

        $link = Link::factory()
            ->for($owner, 'addedBy')
            ->create([
                'title' => 'Original title',
                'url' => 'https://example.com/original',
                'description' => 'Original description',
            ]);

        $link->tags()->attach($tag);

        Livewire::actingAs($owner)
            ->test(Edit::class, ['link' => $link])
            ->set('title', 'Changed title')
            ->set('url', 'ftp://example.com/invalid')
            ->set('tagIds', [])
            ->call('save')
            ->assertHasErrors(['url', 'tagIds']);

        $link->refresh();

        $this->assertSame('Original title', $link->title);
        $this->assertSame('https://example.com/original', $link->url);
        $this->assertDatabaseHas('link_tag', [
            'link_id' => $link->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_owner_can_open_edit_page(): void
    {
        $owner = User::factory()->create();

        $link = Link::factory()
            ->for($owner, 'addedBy')
            ->create();

        $this->actingAs($owner)
            ->get(route('links.edit', $link))
            ->assertOk()
            ->assertSee('Editar enlace');
    }
}
