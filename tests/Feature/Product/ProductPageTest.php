<?php

namespace Tests\Feature\Product;

use App\Livewire\Product\CounterQty;
use App\Livewire\Product\Gallery;
use App\Livewire\Product\Similar;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create([
            'title'     => json_encode(['ua' => 'Категорія', 'ru' => 'Категория']),
            'slug'      => 'test-category',
            'is_active' => true,
        ]);

        $this->product = Product::factory()->create([
            'title'          => json_encode(['ua' => 'Тестовий товар', 'ru' => 'Тестовый товар']),
            'slug'           => 'test-product',
            'price'          => 1500.00,
            'price_old'      => 2000.00,
            'is_active'      => true,
            'quantity'       => 10,
            'category_id'    => $this->category->id,
            'picture'        => 'https://example.com/photo.jpg',
            'other_pictures' => ['https://example.com/photo2.jpg'],
        ]);
    }

    // ─── ProductController ────────────────────────────────────────────────────

    public function test_product_page_renders(): void
    {
        $this->get(route('product.show', $this->product->slug))
            ->assertOk()
            ->assertSee($this->product->t('title'))
            ->assertSeeLivewire(Gallery::class)
            ->assertSeeLivewire(Similar::class)
            ->assertSee('data-js-add-to-cart', false);
    }

    public function test_inactive_product_returns_404(): void
    {
        $inactive = Product::factory()->create([
            'slug'      => 'inactive-product',
            'is_active' => false,
        ]);

        $this->get(route('product.show', $inactive->slug))
            ->assertNotFound();
    }

    public function test_product_page_shows_price(): void
    {
        $this->get(route('product.show', $this->product->slug))
            ->assertOk()
            ->assertSee('1&nbsp;500', false);
    }

    public function test_product_page_shows_discount(): void
    {
        $this->get(route('product.show', $this->product->slug))
            ->assertOk()
            ->assertSee('2&nbsp;000', false);
    }

    public function test_product_page_hides_add_to_cart_when_out_of_stock(): void
    {
        $outOfStock = Product::factory()->create([
            'slug'       => 'out-of-stock',
            'is_active'  => true,
            'quantity'   => 0,
            'category_id' => $this->category->id,
        ]);

        $this->get(route('product.show', $outOfStock->slug))
            ->assertOk()
            ->assertDontSee('data-js-add-to-cart', false);
    }

    // ─── Gallery ─────────────────────────────────────────────────────────────

    public function test_gallery_builds_images_from_product(): void
    {
        Livewire::test(Gallery::class, ['product' => $this->product])
            ->assertSet('images', [
                'https://example.com/photo.jpg',
                'https://example.com/photo2.jpg',
            ])
            ->assertSet('selectedIndex', 0);
    }

    public function test_gallery_navigates_forward(): void
    {
        Livewire::test(Gallery::class, ['product' => $this->product])
            ->call('nextImage')
            ->assertSet('selectedIndex', 1);
    }

    public function test_gallery_navigates_backward(): void
    {
        Livewire::test(Gallery::class, ['product' => $this->product])
            ->call('nextImage')
            ->call('previousImage')
            ->assertSet('selectedIndex', 0);
    }

    public function test_gallery_select_image(): void
    {
        Livewire::test(Gallery::class, ['product' => $this->product])
            ->call('selectImage', 1)
            ->assertSet('selectedIndex', 1);
    }

    public function test_gallery_no_images(): void
    {
        $noImg = Product::factory()->create(['picture' => null, 'other_pictures' => []]);

        Livewire::test(Gallery::class, ['product' => $noImg])
            ->assertSet('images', []);
    }

    // ─── CounterQty ──────────────────────────────────────────────────────────

    public function test_counter_qty_increments(): void
    {
        Livewire::test(CounterQty::class, ['max' => 10])
            ->assertSet('quantity', 1)
            ->call('increment')
            ->assertSet('quantity', 2);
    }

    public function test_counter_qty_decrements(): void
    {
        Livewire::test(CounterQty::class, ['max' => 10])
            ->call('increment')
            ->call('decrement')
            ->assertSet('quantity', 1);
    }

    public function test_counter_qty_does_not_go_below_min(): void
    {
        Livewire::test(CounterQty::class, ['max' => 10])
            ->call('decrement')
            ->assertSet('quantity', 1);
    }

    public function test_counter_qty_does_not_exceed_max(): void
    {
        Livewire::test(CounterQty::class, ['max' => 2])
            ->call('increment')
            ->call('increment')
            ->call('increment')
            ->assertSet('quantity', 2);
    }

    public function test_counter_qty_dispatches_event_on_increment(): void
    {
        Livewire::test(CounterQty::class, ['max' => 10])
            ->call('increment')
            ->assertDispatched('product-qty-changed', quantity: 2);
    }

    // ─── Similar ─────────────────────────────────────────────────────────────

    public function test_similar_shows_products_from_same_category(): void
    {
        $similar = Product::factory()->create([
            'is_active'   => true,
            'quantity'    => 5,
            'category_id' => $this->category->id,
        ]);

        Livewire::test(Similar::class, ['product' => $this->product])
            ->assertSee($similar->t('title'));
    }

    public function test_similar_excludes_current_product(): void
    {
        Livewire::test(Similar::class, ['product' => $this->product])
            ->assertDontSee($this->product->t('title'));
    }

    public function test_similar_uses_interesting_products_when_available(): void
    {
        $interesting = Product::factory()->create([
            'is_active'   => true,
            'category_id' => null,
        ]);

        $this->product->interestingProducts()->attach($interesting->id);

        Livewire::test(Similar::class, ['product' => $this->product])
            ->assertSee($interesting->t('title'));
    }

    public function test_similar_is_empty_when_no_related_products(): void
    {
        $alone = Product::factory()->create([
            'slug'        => 'alone-product',
            'is_active'   => true,
            'category_id' => null,
        ]);

        $component = Livewire::test(Similar::class, ['product' => $alone]);

        $this->assertTrue($component->instance()->render()->getData()['list']->isEmpty());
    }
}
