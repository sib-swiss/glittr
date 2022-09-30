<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\ContactForm;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(ContactForm::class);

        $component->assertStatus(200);
    }
}
