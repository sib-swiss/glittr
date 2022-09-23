<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Repositories;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
