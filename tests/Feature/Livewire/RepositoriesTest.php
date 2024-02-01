<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Repositories;
use Livewire\Livewire;
use Tests\TestCase;

class RepositoriesTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(Repositories::class);

        $component->assertStatus(200);
    }
}
