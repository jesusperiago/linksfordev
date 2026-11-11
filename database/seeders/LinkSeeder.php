<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::query()->get();

        Link::factory()
            ->count(10)
            ->create()
            ->each(function (Link $link) use ($tags): void {
                $link->tags()->attach($tags->random(fake()->numberBetween(1, 3)));
            });
    }
}
