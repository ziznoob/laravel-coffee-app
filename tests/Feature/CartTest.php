<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use Database\Seeders\BrewMethodSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([BrewMethodSeeder::class, ProductSeeder::class]);

        // Huila Highlands 250g: 45.90, stock 50
        $this->variant = Product::where('slug', 'huila-highlands')->first()
            ->variants()->where('weight_grams', 250)->first();
    }

    public function test_cart_page_mounts_react_and_is_not_indexed(): void
    {
        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('data-react="Cart"', false)
            ->assertSee('noindex', false);
    }

    public function test_add_to_cart_form_redirects_back_with_message(): void
    {
        $this->from(route('products.index'))
            ->post(route('cart.items.store'), ['variant_id' => $this->variant->id])
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('status');

        $this->assertSame([$this->variant->id => 1], session('cart'));
    }

    public function test_add_to_cart_via_json_returns_summary_with_server_prices(): void
    {
        $this->postJson(route('cart.items.store'), ['variant_id' => $this->variant->id, 'quantity' => 2])
            ->assertCreated()
            ->assertJsonPath('count', 2)
            ->assertJsonPath('subtotal', 9180)
            ->assertJsonPath('items.0.product_name', 'Huila Highlands')
            ->assertJsonPath('items.0.weight_label', '250g')
            ->assertJsonPath('items.0.line_total', 9180);
    }

    public function test_adding_same_variant_twice_increases_quantity(): void
    {
        $this->postJson(route('cart.items.store'), ['variant_id' => $this->variant->id]);
        $this->postJson(route('cart.items.store'), ['variant_id' => $this->variant->id, 'quantity' => 3])
            ->assertJsonPath('count', 4)
            ->assertJsonCount(1, 'items');
    }

    public function test_cannot_add_more_than_stock(): void
    {
        $this->variant->update(['stock' => 2]);

        $this->postJson(route('cart.items.store'), ['variant_id' => $this->variant->id, 'quantity' => 3])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');
    }

    public function test_cannot_add_inactive_product(): void
    {
        $this->variant->product->update(['is_active' => false]);

        $this->postJson(route('cart.items.store'), ['variant_id' => $this->variant->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('variant');
    }

    public function test_cannot_add_unknown_variant(): void
    {
        $this->postJson(route('cart.items.store'), ['variant_id' => '00000000-0000-0000-0000-000000000000'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('variant_id');
    }

    public function test_update_quantity(): void
    {
        $this->postJson(route('cart.items.store'), ['variant_id' => $this->variant->id]);

        $this->patchJson(route('cart.items.update', $this->variant), ['quantity' => 5])
            ->assertOk()
            ->assertJsonPath('count', 5)
            ->assertJsonPath('subtotal', 4590 * 5);
    }

    public function test_quantity_must_be_at_least_one_and_within_stock(): void
    {
        $this->postJson(route('cart.items.store'), ['variant_id' => $this->variant->id]);

        $this->patchJson(route('cart.items.update', $this->variant), ['quantity' => 0])
            ->assertJsonValidationErrors('quantity');

        $this->patchJson(route('cart.items.update', $this->variant), ['quantity' => 51])
            ->assertJsonValidationErrors('quantity');
    }

    public function test_cannot_update_item_not_in_cart(): void
    {
        $this->patchJson(route('cart.items.update', $this->variant), ['quantity' => 2])
            ->assertUnprocessable();
    }

    public function test_remove_item_and_clear_cart(): void
    {
        $other = $this->variant->product->variants()->where('weight_grams', 500)->first();

        $this->postJson(route('cart.items.store'), ['variant_id' => $this->variant->id]);
        $this->postJson(route('cart.items.store'), ['variant_id' => $other->id]);

        $this->deleteJson(route('cart.items.destroy', $this->variant))
            ->assertOk()
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.weight_label', '500g');

        $this->deleteJson(route('cart.clear'))
            ->assertOk()
            ->assertJsonPath('count', 0)
            ->assertJsonCount(0, 'items');
    }

    public function test_deactivated_products_drop_out_of_the_cart(): void
    {
        $this->postJson(route('cart.items.store'), ['variant_id' => $this->variant->id]);
        $this->variant->product->update(['is_active' => false]);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertViewHas('cart', fn (array $cart) => $cart['count'] === 0 && $cart['items'] === []);
    }

    public function test_header_shows_cart_count(): void
    {
        $this->postJson(route('cart.items.store'), ['variant_id' => $this->variant->id, 'quantity' => 3]);

        $this->get(route('products.index'))
            ->assertSee('data-cart-count', false)
            ->assertSeeInOrder(['Cart', '3']);
    }
}
