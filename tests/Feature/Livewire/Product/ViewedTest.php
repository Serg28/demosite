<?php

namespace Tests\Feature\Livewire\Product;

use App\Livewire\Product\Viewed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ViewedTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(Viewed::class)
            ->assertStatus(200);
    }
}
