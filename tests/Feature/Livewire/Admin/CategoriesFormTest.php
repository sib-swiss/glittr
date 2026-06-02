<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\CategoryForm;
use Livewire\Livewire;
use Tests\TestCase;

class CategoriesFormTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(CategoryForm::class, ['categoryId' => null, 'cancelEvent' => 'cancelCategory']);

        $component->assertStatus(200);
    }
}
