<?php

namespace Tests\Feature;

use App\Models\Tree;
use Tests\TestCase;

class TreeTest extends TestCase
{
    public function testOpenPages(): void
    {
        $trees = Tree::active()->get();

        foreach ($trees as $tree) {
            $response = $this->get($tree->getUrl());
            $response->assertStatus(200);
        }
    }
}
