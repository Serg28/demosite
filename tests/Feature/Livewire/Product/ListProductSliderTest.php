<?php

namespace Tests\Feature\Livewire\Product;

use App\Livewire\Product\ListProductSlider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ListProductSliderTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(ListProductSlider::class)
            ->assertStatus(200);
    }
}
