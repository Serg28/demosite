<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function testSend(): void
    {
        $product = Product::first();

        $response = $this->post(route('comment', $product), [
            'name' => 'name',
            'message' => 'message',
            'g_recaptcha_response' => 'required|recaptcha',
            'rating' => 3,
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('comments',
            [
                'name' => 'name',
                'message' => 'message',
                'product_id' => $product->id,
            ]
        );
    }

    public function testShowCommentOnProduct(): void
    {
        $product = Product::orderByRaw('RAND()')->first();

        $this->post(route('comment', $product), [
            'name' => 'name',
            'message' => 'message',
            'g_recaptcha_response' => 'required|recaptcha',
            'rating' => 3,
        ]);

        $this->post(route('comment', $product), [
            'name' => 'name2',
            'message' => 'message2',
            'g_recaptcha_response' => 'required|recaptcha',
            'rating' => 4,
        ]);

        $comments = \App\Models\Comment::where('product_id', $product->id)->get();

        foreach ($comments as $comment) {
            $comment->update(['is_active' => 1]);
        }

        $product = Product::find($product->id);

        $response = $this->get($product->getUrl());

        $response->assertStatus(200)->assertSee('message2');
        $this->assertEquals($product->comment()->count(), 2);
        $this->assertEquals($product->comment()->getRating(), 3.5);
        $this->assertEquals($product->comment()->ratingPercent(), 70);
    }
}
