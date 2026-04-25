<?php

namespace Tests\Feature\Livewire\Form;

use App\Livewire\Form\RequestApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class RequestApplicationTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(RequestApplication::class)
            ->assertStatus(200);
    }
}
