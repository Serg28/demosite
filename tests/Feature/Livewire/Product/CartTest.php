<?php

namespace Tests\Feature\Livewire\Product;

use App\Livewire\Product\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class CartTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(Card::class)
            ->assertStatus(200);
    }
}
