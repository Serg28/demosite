<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Promotion;
use App\Models\Tree;
use Tests\TestCase;

class BlogTest extends TestCase
{
    public function testOpenList()
    {
        $tree = Tree::whereTemplate('news')->first();
        $blog = News::latest()->first();
        $response = $this->get($tree->getUrl());
        $response->assertStatus(200)->assertSee($tree->t('title'))->assertSee($blog->t('title'));
    }

   public function testOpenBLogPage()
   {
       $blog = News::first();
       $response = $this->get($blog->getUrl());
       $response->assertStatus(200)->assertSee($blog->t('title'));
   }

   public function testPromotion()
   {
       $promotion = Promotion::first();
       $response = $this->get($promotion->getUrl());
       $response->assertStatus(200);
   }
}
