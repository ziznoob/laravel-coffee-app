<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\BrewMethodSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([BrewMethodSeeder::class, ProductSeeder::class]);

        // 2 x 45.90 + 1 x 100.90 = 192.70
        $huila = Product::where('slug', 'huila-highlands')->first()->variants()->where('weight_grams', 250)->first();
        $dakLak = Product::where('slug', 'dak-lak-thunder')->first()->variants()->where('weight_grams', 1000)->first();

        $this->order = Order::create(['total' => 19270]);
        $this->order->items()->createMany([
            ['product_variant_id' => $huila->id, 'quantity' => 2, 'unit_price' => $huila->price],
            ['product_variant_id' => $dakLak->id, 'quantity' => 1, 'unit_price' => $dakLak->price],
        ]);
    }

    public function test_guests_cannot_see_orders(): void
    {
        $this->get(route('admin.orders.index'))->assertRedirect(route('login'));
    }

    public function test_new_orders_default_to_pending(): void
    {
        $this->assertSame(OrderStatus::Pending, $this->order->fresh()->status);
    }

    public function test_orders_get_a_random_public_order_number(): void
    {
        $other = Order::create(['total' => 0]);

        $this->assertMatchesRegularExpression('/^ORD-[A-Z0-9]{8}$/', $this->order->order_number);
        $this->assertNotSame($this->order->order_number, $other->order_number);
    }

    public function test_order_list_shows_products_purchased(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($this->order->order_number)
            ->assertSeeInOrder(['Huila Highlands', '250g', '45.90', '2', '91.80'])
            ->assertSeeInOrder(['Dak Lak Thunder', '1kg', '100.90', '1'])
            ->assertSee('192.70')
            ->assertSee('Pending');
    }

    public function test_order_list_shows_price_paid_even_if_product_price_changes(): void
    {
        $this->order->items->first()->variant->update(['price' => 9999]);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.orders.index'))
            ->assertSee('45.90')
            ->assertDontSee('99.99');
    }

    public function test_admin_can_change_order_status(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('admin.orders.index'))
            ->patch(route('admin.orders.status', $this->order), ['status' => 'shipped'])
            ->assertRedirect(route('admin.orders.index'))
            ->assertSessionHas('status', "Order {$this->order->order_number} is now Shipped.");

        $this->assertSame(OrderStatus::Shipped, $this->order->fresh()->status);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->patch(route('admin.orders.status', $this->order), ['status' => 'teleported'])
            ->assertSessionHasErrors('status');

        $this->assertSame(OrderStatus::Pending, $this->order->fresh()->status);
    }

    public function test_guests_cannot_change_status(): void
    {
        $this->patch(route('admin.orders.status', $this->order), ['status' => 'shipped'])
            ->assertRedirect(route('login'));

        $this->assertSame(OrderStatus::Pending, $this->order->fresh()->status);
    }
}
