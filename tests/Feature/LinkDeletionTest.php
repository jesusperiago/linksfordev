<?php

namespace Tests\Feature;

use App\Livewire\Links\Index;
use App\Models\Link;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LinkDeletionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_owner_can_delete_link_and_tag_associations(): void
    {
        $owner = User::factory()->create();
        $tag = Tag::factory()->create();

        $link = Link::factory()
            ->for($owner, 'addedBy')
            ->create([
                'title' => 'Resource to delete',
            ]);

        $link->tags()->attach($tag);

        Livewire::actingAs($owner)
            ->test(Index::class)
            ->call('deleteLink', $link->id)
            ->assertDontSee($link->title);

        $this->assertDatabaseMissing('links', [
            'id' => $link->id,
        ]);
        $this->assertDatabaseMissing('link_tag', [
            'link_id' => $link->id,
            'tag_id' => $tag->id,
        ]);
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
        ]);
    }

    public function test_user_cannot_delete_link_added_by_someone_else(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $tag = Tag::factory()->create();

        $link = Link::factory()
            ->for($owner, 'addedBy')
            ->create();

        $link->tags()->attach($tag);

        Livewire::actingAs($otherUser)
            ->test(Index::class)
            ->call('deleteLink', $link->id)
            ->assertForbidden();

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'user_id' => $owner->id,
        ]);
        $this->assertDatabaseHas('link_tag', [
            'link_id' => $link->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_guest_cannot_delete_a_link(): void
    {
        $owner = User::factory()->create();

        $link = Link::factory()
            ->for($owner, 'addedBy')
            ->create();

        Livewire::test(Index::class)
            ->call('deleteLink', $link->id)
            ->assertForbidden();

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
        ]);
    }

    public function test_delete_button_is_only_visible_to_link_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $link = Link::factory()
            ->for($owner, 'addedBy')
            ->create([
                'title' => 'Owned resource',
            ]);

        Livewire::actingAs($owner)
            ->test(Index::class)
            ->assertSee('Eliminar');

        Livewire::actingAs($otherUser)
            ->test(Index::class)
            ->assertDontSee('Eliminar');

        Livewire::test(Index::class)
            ->assertDontSee('Eliminar');
    }
}
