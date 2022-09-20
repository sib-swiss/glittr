<?php

namespace Tests\Feature\Livewire\Admin;

use App\Http\Livewire\Admin\CategoriesForm;
use Livewire\Livewire;
use Tests\TestCase;

class CategoriesFormTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(CategoriesForm::class);

        $component->assertStatus(200);
    }
}
