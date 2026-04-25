<?php

namespace Tests\Feature;

use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function testOpenProduct()
    {
        $response = $this->get('/zootovary/dlya-ptic/vitaminy/krana-virobnik-tovaru=belgiya/price=119-758/sort=popularity');

        $response->assertStatus(200);
    }

    public function testChangeProductsView()
    {
        $response = $this->get(route('change.products.view', 'list'));
        $response->assertStatus(200);
    }
}
