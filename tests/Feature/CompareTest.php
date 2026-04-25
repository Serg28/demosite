<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class CompareTest extends TestCase
{
    public function testIndexPage()
    {
        $response = $this->get(route('compare'));

        $response->assertStatus(200)->assertSee(__t('Відсутні товари для порівняння'));
    }

    public function testAddProductInCompare()
    {
        $product = Product::first();
        $response = $this->post(route('compare.add', $product));

        $response->assertStatus(200)->assertJson(['status' => 'success', 'count' => 1]);
    }

    public function testRemoveProductInCompare()
    {
        $product = Product::first();
        $this->post(route('compare.add', $product));
        $response = $this->post(route('compare.delete', $product));

        $response->assertStatus(200)->assertJson(['status' => 'success', 'count' => 0]);
    }
}
