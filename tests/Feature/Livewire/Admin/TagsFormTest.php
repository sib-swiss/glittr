<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\TagForm;
use Livewire\Livewire;
use Tests\TestCase;

class TagsFormTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(TagForm::class, ['tagId' => null, 'cancelEvent' => 'cancelTag']);

        $component->assertStatus(200);
    }
}
