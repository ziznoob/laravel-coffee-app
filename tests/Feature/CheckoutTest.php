<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Database\Seeders\BrewMethodSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private ProductVariant $huila; // 250g, 45.90

    private ProductVariant $dakLak; // 1kg, 100.90

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([BrewMethodSeeder::class, ProductSeeder::class]);

        $this->huila = Product::where('slug', 'huila-highlands')->first()->variants()->where('weight_grams', 250)->first();
        $this->dakLak = Product::where('slug', 'dak-lak-thunder')->first()->variants()->where('weight_grams', 1000)->first();
    }

    private function addToCart(ProductVariant $variant, int $quantity): void
    {
        $this->postJson(route('cart.items.store'), ['variant_id' => $variant->id, 'quantity' => $quantity])->assertCreated();
    }

    public function test_checkout_creates_order_reduces_stock_and_empties_cart(): void
    {
        $this->addToCart($this->huila, 2);
        $this->addToCart($this->dakLak, 1);

        $response = $this->postJson(route('cart.checkout'))->assertCreated();

        $order = Order::with('items')->sole();
        $response->assertJsonPath('order_number', $order->order_number)
            ->assertJsonPath('redirect', route('cart.success'));

        $this->get(route('cart.success'))
            ->assertOk()
            ->assertSee('Order confirmed')
            ->assertSee($order->order_number)
            ->assertSee('192.70')
            ->assertSee('Return to home');

        $this->get(route('cart.success'))->assertRedirect(route('homepage'));

        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertSame(4590 * 2 + 10090, $order->total);
        $this->assertCount(2, $order->items);
        $this->assertSame(4590, $order->items->firstWhere('product_variant_id', $this->huila->id)->unit_price);

        $this->assertSame(48, $this->huila->fresh()->stock);
        $this->assertSame(49, $this->dakLak->fresh()->stock);

        $this->assertSame([], session('cart', []));
    }

    public function test_success_page_without_a_checkout_redirects_home(): void
    {
        $this->get(route('cart.success'))->assertRedirect(route('homepage'));
    }

    public function test_cannot_checkout_empty_cart(): void
    {
        $this->postJson(route('cart.checkout'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cart');

        $this->assertSame(0, Order::count());
    }

    public function test_nothing_is_saved_if_one_item_ran_out_of_stock(): void
    {
        $this->addToCart($this->huila, 2);
        $this->addToCart($this->dakLak, 3);

        $this->dakLak->update(['stock' => 1]);

        $this->postJson(route('cart.checkout'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');

        $this->assertSame(0, Order::count());
        $this->assertSame(50, $this->huila->fresh()->stock);
        $this->assertSame(1, $this->dakLak->fresh()->stock);
        $this->assertNotEmpty(session('cart'));
    }

    public function test_cannot_checkout_deactivated_product(): void
    {
        $this->addToCart($this->huila, 1);
        $this->huila->product->update(['is_active' => false]);

        $this->postJson(route('cart.checkout'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('variant');

        $this->assertSame(0, Order::count());
    }

    public function test_new_order_appears_in_admin_order_list(): void
    {
        $this->addToCart($this->huila, 2);
        $this->postJson(route('cart.checkout'))->assertCreated();

        $this->actingAs(\App\Models\User::factory()->create())
            ->get(route('admin.orders.index'))
            ->assertSee('Huila Highlands')
            ->assertSee('91.80');
    }
}
