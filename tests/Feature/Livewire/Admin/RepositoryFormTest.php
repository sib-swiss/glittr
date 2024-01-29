<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\RepositoryForm;
use Livewire\Livewire;
use Tests\TestCase;

class RepositoryFormTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(RepositoryForm::class);

        $component->assertStatus(200);
    }
}
