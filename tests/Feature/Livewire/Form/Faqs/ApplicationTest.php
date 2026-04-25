<?php

namespace Tests\Feature\Livewire\Form\Faqs;

use App\Livewire\Form\Faqs\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(Application::class)
            ->assertStatus(200);
    }
}
