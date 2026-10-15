<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    private const IMAGE_DIR = 'images/products';

    public function run(): void
    {
        foreach (Product::all() as $product) {
            $path = self::IMAGE_DIR."/{$product->slug}.png";

            if (! file_exists(public_path($path))) {
                $this->command?->warn("No image for {$product->name}: expected public/{$path}");

                continue;
            }

            $product->update(['image' => $path]);
        }
    }
}
