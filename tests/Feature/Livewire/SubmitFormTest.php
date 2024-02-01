<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubmitForm;
use Livewire\Livewire;
use Tests\TestCase;

class SubmitFormTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(SubmitForm::class);

        $component->assertStatus(200);
    }
}
