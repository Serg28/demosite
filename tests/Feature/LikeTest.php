<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class LikeTest extends TestCase
{
    public function testAddProductUnAuthUser(): void
    {
        $product = Product::first();

        $response = $this->post(route('like.add', $product));
        $response->assertStatus(401);
    }

    public function testAddProduct(): void
    {
        $user = $this->authUser();

        $product = Product::first();

        $response = $this->post(route('like.add', $product));
        $countLikes = Product::whereLikedBy(app('user')->id)->with('likeCounter')->count();

        $response->assertStatus(200)->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('likeable_likes',
            [
                'likeable_id' => $product->id,
                'user_id' => $user->id,
            ]
        );

        $this->assertEquals($countLikes, 1);
    }

    public function testRemoveProduct(): void
    {
        $this->authUser();

        $product = Product::first();
        $this->post(route('like.add', $product));

        $response = $this->post(route('like.delete', $product));
        $countLikes = Product::whereLikedBy(app('user')->id)->with('likeCounter')->count();

        $this->assertEquals($countLikes, 0);
        $response->assertStatus(200)->assertJson(['status' => 'success']);
    }
}
