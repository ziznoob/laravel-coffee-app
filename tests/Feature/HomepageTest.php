<?php

namespace Tests\Feature;

use Database\Seeders\BrewMethodSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_three_products_and_view_more_link(): void
    {
        $this->seed([BrewMethodSeeder::class, ProductSeeder::class]);

        $this->get(route('homepage'))
            ->assertOk()
            ->assertViewHas('products', fn ($products) => $products->count() === 3)
            ->assertSee('View more')
            ->assertSee(route('products.index'), false)
            ->assertSee('Add to cart');
    }

    public function test_homepage_works_with_no_products(): void
    {
        $this->get(route('homepage'))->assertOk();
    }
}
