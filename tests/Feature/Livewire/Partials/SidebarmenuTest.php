<?php

namespace Tests\Feature\Livewire\Partials;

use App\Livewire\Partials\Sidebarmenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class SidebarmenuTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(Sidebarmenu::class)
            ->assertStatus(200);
    }
}
