<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagNames = [
            'php',
            'laravel',
            'livewire',
            'javascript',
            'typescript',
            'testing',
            'devops',
            'security',
        ];

        foreach ($tagNames as $name) {
            Tag::query()->firstOrCreate(['name' => $name]);
        }
    }
}
