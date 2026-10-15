<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\BrewMethodSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([BrewMethodSeeder::class, ProductSeeder::class]);
    }

    public function test_get_all_products_returns_only_active_products_with_relations(): void
    {
        Product::where('slug', 'dak-lak-thunder')->update(['is_active' => false]);

        $products = Product::getAllProducts();

        $this->assertCount(18, $products);
        $this->assertFalse($products->contains('slug', 'dak-lak-thunder'));
        $this->assertTrue($products->first()->relationLoaded('variants'));
        $this->assertTrue($products->first()->relationLoaded('brewMethods'));
    }

    public function test_products_page_lists_products_with_prices(): void
    {
        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Huila Highlands')
            ->assertSee('Huila, Colombia')
            ->assertSee('45.90')
            ->assertSee('1kg');
    }
}
