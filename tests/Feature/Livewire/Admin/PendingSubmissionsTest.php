<?php

namespace Tests\Feature\Livewire\Admin;

use App\Http\Livewire\Admin\PendingSubmissions;
use Livewire\Livewire;
use Tests\TestCase;

class PendingSubmissionsTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(PendingSubmissions::class);

        $component->assertStatus(200);
    }
}
