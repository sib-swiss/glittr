<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\RepositoriesList;
use Livewire\Livewire;
use Tests\TestCase;

class RepositoriesListTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(RepositoriesList::class);

        $component->assertStatus(200);
    }
}
