<?php

use Livewire\Component;

new class extends Component
{
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }

    public function decrement(): void
    {
        $this->count--;
    }
};
?>

<div class="flex items-center justify-center gap-4 my-4">
    <button
        wire:click="decrement"
        class="w-10 h-10 rounded-full bg-red-500 text-white text-xl font-bold hover:bg-red-600 transition"
    >
        &minus;
    </button>

    <span class="text-3xl font-bold tabular-nums w-16 text-center" wire:key="count-display">
        {{ $count }}
    </span>

    <button
        wire:click="increment"
        class="w-10 h-10 rounded-full bg-green-500 text-white text-xl font-bold hover:bg-green-600 transition"
    >
        +
    </button>
</div>
