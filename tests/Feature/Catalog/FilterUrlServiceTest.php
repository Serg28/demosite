<?php

namespace Tests\Feature\Catalog;

use App\Services\FilterUrlService;
use Tests\TestCase;

class FilterUrlServiceTest extends TestCase
{
    private FilterUrlService $service;

    /** @var array<string, array{char_id: int, is_range_type: bool, options: array<string, int>}> */
    private array $slugMap;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FilterUrlService;
        $this->slugMap = [
            'color' => ['char_id' => 5, 'is_range_type' => false, 'options' => ['red' => 10, 'blue' => 11, 'green' => 12]],
            'brand' => ['char_id' => 6, 'is_range_type' => false, 'options' => ['apple' => 20, 'samsung' => 21]],
            'year' => ['char_id' => 7, 'is_range_type' => true,  'options' => []],
        ];
    }

    // ─── parseFilterPath ────────────────────────────────────────────────────

    public function test_parse_empty_path_returns_empty_filters(): void
    {
        $result = $this->service->parseFilterPath('', $this->slugMap);

        $this->assertSame(['characteristics' => [], 'min_price' => null, 'max_price' => null, 'in_stock' => false], $result);
    }

    public function test_parse_in_stock_segment(): void
    {
        $result = $this->service->parseFilterPath('in_stock=1', $this->slugMap);

        $this->assertTrue($result['in_stock']);
    }

    public function test_parse_in_stock_with_other_filters(): void
    {
        $result = $this->service->parseFilterPath('color=red/in_stock=1', $this->slugMap);

        $this->assertTrue($result['in_stock']);
        $this->assertSame([5 => [10]], $result['characteristics']);
    }

    public function test_parse_in_stock_zero_is_false(): void
    {
        $result = $this->service->parseFilterPath('in_stock=0', $this->slugMap);

        $this->assertFalse($result['in_stock']);
    }

    // ─── buildToggleInStockUrl ───────────────────────────────────────────────

    public function test_toggle_instock_adds_segment_when_absent(): void
    {
        $url = $this->service->buildToggleInStockUrl('/catalog/phones', '');

        $this->assertSame('/catalog/phones/in_stock=1/', $url);
    }

    public function test_toggle_instock_removes_segment_when_present(): void
    {
        $url = $this->service->buildToggleInStockUrl('/catalog/phones', 'in_stock=1/');

        $this->assertSame('/catalog/phones/', $url);
    }

    public function test_toggle_instock_preserves_other_segments(): void
    {
        $url = $this->service->buildToggleInStockUrl('/catalog/phones', 'color=red/');

        $this->assertSame('/catalog/phones/color=red/in_stock=1/', $url);
    }

    public function test_parse_single_option(): void
    {
        $result = $this->service->parseFilterPath('color=red', $this->slugMap);

        $this->assertSame([5 => [10]], $result['characteristics']);
    }

    public function test_parse_multiple_options_in_same_group(): void
    {
        $result = $this->service->parseFilterPath('color=red,blue', $this->slugMap);

        $this->assertSame([5 => [10, 11]], $result['characteristics']);
    }

    public function test_parse_multiple_groups(): void
    {
        $result = $this->service->parseFilterPath('color=red/brand=apple', $this->slugMap);

        $this->assertSame([5 => [10], 6 => [20]], $result['characteristics']);
    }

    public function test_parse_price_segment(): void
    {
        $result = $this->service->parseFilterPath('price=100-500', $this->slugMap);

        $this->assertSame(100, $result['min_price']);
        $this->assertSame(500, $result['max_price']);
    }

    public function test_parse_range_characteristic(): void
    {
        $result = $this->service->parseFilterPath('year=2018-2023', $this->slugMap);

        $this->assertSame(['type' => 'range', 'min' => 2018, 'max' => 2023], $result['characteristics'][7]);
    }

    public function test_parse_ignores_unknown_slugs(): void
    {
        $result = $this->service->parseFilterPath('unknown=foo/color=red', $this->slugMap);

        $this->assertSame([5 => [10]], $result['characteristics']);
    }

    public function test_parse_combined_filters_and_price(): void
    {
        $result = $this->service->parseFilterPath('color=red,blue/brand=apple/price=1000-9999', $this->slugMap);

        $this->assertSame([5 => [10, 11], 6 => [20]], $result['characteristics']);
        $this->assertSame(1000, $result['min_price']);
        $this->assertSame(9999, $result['max_price']);
    }

    // ─── buildOptionUrl ─────────────────────────────────────────────────────

    public function test_build_option_url_adds_new_option(): void
    {
        $url = $this->service->buildOptionUrl('/catalog/phones', '', 'color', 'red');

        $this->assertSame('/catalog/phones/color=red/', $url);
    }

    public function test_build_option_url_toggles_existing_option_off(): void
    {
        $url = $this->service->buildOptionUrl('/catalog/phones', 'color=red/', 'color', 'red');

        $this->assertSame('/catalog/phones/', $url);
    }

    public function test_build_option_url_adds_to_existing_group(): void
    {
        $url = $this->service->buildOptionUrl('/catalog/phones', 'color=red/', 'color', 'blue');

        $this->assertSame('/catalog/phones/color=red,blue/', $url);
    }

    public function test_build_option_url_removes_one_of_multiple_options(): void
    {
        $url = $this->service->buildOptionUrl('/catalog/phones', 'color=red,blue/', 'color', 'red');

        $this->assertSame('/catalog/phones/color=blue/', $url);
    }

    public function test_build_option_url_preserves_other_segments(): void
    {
        $url = $this->service->buildOptionUrl('/catalog/phones', 'brand=apple/color=red/', 'color', 'blue');

        $this->assertSame('/catalog/phones/brand=apple/color=red,blue/', $url);
    }

    // ─── buildRangeUrl ──────────────────────────────────────────────────────

    public function test_build_range_url_adds_range_segment(): void
    {
        $url = $this->service->buildRangeUrl('/catalog/cars', '', 'year', 2018, 2023);

        $this->assertSame('/catalog/cars/year=2018-2023/', $url);
    }

    public function test_build_range_url_updates_existing_segment(): void
    {
        $url = $this->service->buildRangeUrl('/catalog/cars', 'year=2010-2020/', 'year', 2018, 2023);

        $this->assertSame('/catalog/cars/year=2018-2023/', $url);
    }

    public function test_build_range_url_removes_segment_when_null(): void
    {
        $url = $this->service->buildRangeUrl('/catalog/cars', 'year=2018-2023/', 'year', null, null);

        $this->assertSame('/catalog/cars/', $url);
    }

    // ─── buildClearUrl ──────────────────────────────────────────────────────

    public function test_build_clear_url_returns_base_path(): void
    {
        $url = $this->service->buildClearUrl('/catalog/phones');

        $this->assertSame('/catalog/phones/', $url);
    }
}
