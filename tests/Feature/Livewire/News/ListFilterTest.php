<?php

namespace Tests\Feature\Livewire\News;

use App\Livewire\News\ListFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ListFilterTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(ListFilter::class)
            ->assertStatus(200);
    }
}
