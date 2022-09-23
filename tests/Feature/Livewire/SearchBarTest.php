<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\SearchBar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
