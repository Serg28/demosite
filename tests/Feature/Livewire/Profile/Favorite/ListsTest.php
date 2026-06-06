<?php

namespace Tests\Feature\Livewire\Profile\Favorite;

use App\Livewire\Favorite\Lists;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class ListsTest extends TestCase
{
    public function test_renders_successfully_for_auth_user(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Lists::class)
            ->assertStatus(200);
    }
}
