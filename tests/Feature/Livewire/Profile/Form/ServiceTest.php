<?php

namespace Tests\Feature\Livewire\Profile\Form;

use App\Livewire\Profile\Form\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(Service::class)
            ->assertStatus(200);
    }
}
