<?php

namespace Tests\Feature\Livewire;

use App\Livewire\TagSelect;
use Livewire\Livewire;
use Tests\TestCase;

class TagSelectTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(TagSelect::class);

        $component->assertStatus(200);
    }
}
