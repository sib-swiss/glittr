<?php

namespace Tests\Feature\Livewire\Admin;

use App\Http\Livewire\Admin\TagsList;
use Livewire\Livewire;
use Tests\TestCase;

class TagsListTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(TagsList::class);

        $component->assertStatus(200);
    }
}
