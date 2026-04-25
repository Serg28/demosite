<?php

namespace Tests\Feature\Livewire\Form;

use App\Livewire\Form\ServiceApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceApplicationTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(ServiceApplication::class)
            ->assertStatus(200);
    }
}
