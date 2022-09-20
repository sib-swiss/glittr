<?php

namespace Tests\Feature\Livewire\Admin;

use App\Http\Livewire\Admin\TagsForm;
use Livewire\Livewire;
use Tests\TestCase;

class TagsFormTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(TagsForm::class);

        $component->assertStatus(200);
    }
}
