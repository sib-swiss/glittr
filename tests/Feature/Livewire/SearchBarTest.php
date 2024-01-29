<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SearchBar;
use Livewire\Livewire;
use Tests\TestCase;

class SearchBarTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(SearchBar::class);

        $component->assertStatus(200);
    }
}
