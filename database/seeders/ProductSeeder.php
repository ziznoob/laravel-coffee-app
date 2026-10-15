<?php

namespace Database\Seeders;

use App\Enums\BeanType;
use App\Models\BrewMethod;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    // weight in grams => price multiplier of the 250g price
    private const SIZES = [
        250 => 1.0,
        500 => 1.9,
        1000 => 3.6,
    ];

    public function run(): void
    {
        $brewMethods = BrewMethod::pluck('id', 'slug');

        foreach ($this->products() as $data) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'origin' => $data['origin'],
                    'roast_level' => $data['roast_level'],
                    'flavour' => $data['flavour'],
                    'description' => $data['description'],
                    'is_active' => true,
                ],
            );

            foreach (self::SIZES as $grams => $multiplier) {
                $product->variants()->updateOrCreate(
                    ['weight_grams' => $grams],
                    [
                        'price' => (int) (round($data['price_250g'] * $multiplier) * 100 - 10),
                        'stock' => 50,
                    ],
                );
            }

            $product->brewMethods()->sync(
                collect($data['best_for'])->map(fn ($slug) => $brewMethods[$slug])
            );
        }
    }

    private function products(): array
    {
        return [
            // Arabica
            [
                'name' => 'Yirgacheffe Sunrise',
                'type' => BeanType::Arabica,
                'origin' => 'Yirgacheffe, Ethiopia',
                'roast_level' => 'light',
                'flavour' => ['jasmine', 'lemon', 'bergamot', 'honey'],
                'description' => 'Bright and floral beans from the birthplace of coffee, washed and sun-dried in the Ethiopian highlands.',
                'price_250g' => 52,
                'best_for' => ['pour-over', 'drip-filter', 'cold-brew'],
            ],
            [
                'name' => 'Huila Highlands',
                'type' => BeanType::Arabica,
                'origin' => 'Huila, Colombia',
                'roast_level' => 'medium',
                'flavour' => ['caramel', 'red apple', 'cocoa'],
                'description' => 'A balanced, sweet cup grown on the Andean slopes of southern Colombia.',
                'price_250g' => 46,
                'best_for' => ['espresso', 'pour-over', 'french-press'],
            ],
            [
                'name' => 'Nyeri Crimson',
                'type' => BeanType::Arabica,
                'origin' => 'Nyeri, Kenya',
                'roast_level' => 'light',
                'flavour' => ['blackcurrant', 'grapefruit', 'brown sugar'],
                'description' => 'Juicy and vibrant, grown in red volcanic soil at the foot of Mount Kenya.',
                'price_250g' => 55,
                'best_for' => ['pour-over', 'drip-filter'],
            ],
            [
                'name' => 'Cerrado Velvet',
                'type' => BeanType::Arabica,
                'origin' => 'Cerrado Mineiro, Brazil',
                'roast_level' => 'medium-dark',
                'flavour' => ['milk chocolate', 'hazelnut', 'peanut'],
                'description' => 'Smooth, nutty and low in acidity from the sunny savanna plateaus of Minas Gerais.',
                'price_250g' => 38,
                'best_for' => ['espresso', 'moka-pot', 'french-press'],
            ],
            [
                'name' => 'Antigua Volcano',
                'type' => BeanType::Arabica,
                'origin' => 'Antigua, Guatemala',
                'roast_level' => 'medium',
                'flavour' => ['dark chocolate', 'orange peel', 'cinnamon'],
                'description' => 'Grown in a valley ringed by three volcanoes, giving a rich, spicy and full-bodied cup.',
                'price_250g' => 48,
                'best_for' => ['espresso', 'french-press', 'drip-filter'],
            ],
            [
                'name' => 'Blue Mountain Mist',
                'type' => BeanType::Arabica,
                'origin' => 'Blue Mountains, Jamaica',
                'roast_level' => 'medium',
                'flavour' => ['milk chocolate', 'floral', 'sweet herbs'],
                'description' => 'One of the world\'s rarest coffees, grown in cool mountain mist for an exceptionally clean, mild cup.',
                'price_250g' => 120,
                'best_for' => ['pour-over', 'drip-filter'],
            ],
            [
                'name' => 'Mandheling Earth',
                'type' => BeanType::Arabica,
                'origin' => 'North Sumatra, Indonesia',
                'roast_level' => 'dark',
                'flavour' => ['earthy', 'cedar', 'dark chocolate'],
                'description' => 'Wet-hulled Sumatran beans with a heavy body and deep, earthy character.',
                'price_250g' => 44,
                'best_for' => ['french-press', 'cold-brew', 'espresso'],
            ],

            // Robusta
            [
                'name' => 'Dak Lak Thunder',
                'type' => BeanType::Robusta,
                'origin' => 'Dak Lak, Vietnam',
                'roast_level' => 'dark',
                'flavour' => ['dark chocolate', 'roasted nuts', 'molasses'],
                'description' => 'Bold, strong robusta from Vietnam\'s Central Highlands, perfect for traditional iced coffee.',
                'price_250g' => 28,
                'best_for' => ['vietnamese-phin', 'espresso', 'cold-brew'],
            ],
            [
                'name' => 'Lampung Ember',
                'type' => BeanType::Robusta,
                'origin' => 'Lampung, Indonesia',
                'roast_level' => 'dark',
                'flavour' => ['cocoa', 'smoky', 'earthy'],
                'description' => 'A smoky, intense robusta from the southern tip of Sumatra.',
                'price_250g' => 26,
                'best_for' => ['espresso', 'moka-pot'],
            ],
            [
                'name' => 'Coorg Monsoon',
                'type' => BeanType::Robusta,
                'origin' => 'Coorg, India',
                'roast_level' => 'medium-dark',
                'flavour' => ['spice', 'dark chocolate', 'malt'],
                'description' => 'Shade-grown under pepper vines and forest trees in the hills of Karnataka.',
                'price_250g' => 30,
                'best_for' => ['espresso', 'cold-brew', 'french-press'],
            ],
            [
                'name' => 'Lake Victoria Bold',
                'type' => BeanType::Robusta,
                'origin' => 'Lake Victoria Basin, Uganda',
                'roast_level' => 'medium-dark',
                'flavour' => ['dark cocoa', 'brown sugar', 'woody'],
                'description' => 'Grown near the home of wild robusta, with a full body and clean finish.',
                'price_250g' => 27,
                'best_for' => ['espresso', 'french-press'],
            ],

            // Liberica
            [
                'name' => 'Johor Jackfruit Gold',
                'type' => BeanType::Liberica,
                'origin' => 'Johor, Malaysia',
                'roast_level' => 'medium-dark',
                'flavour' => ['jackfruit', 'smoky', 'dark chocolate'],
                'description' => 'Malaysia\'s signature bean: large, rare liberica with a distinctive fruity, smoky aroma.',
                'price_250g' => 36,
                'best_for' => ['french-press', 'drip-filter', 'cold-brew'],
            ],
            [
                'name' => 'Batangas Barako',
                'type' => BeanType::Liberica,
                'origin' => 'Batangas, Philippines',
                'roast_level' => 'dark',
                'flavour' => ['bold', 'woody', 'anise'],
                'description' => 'The legendary "Kapeng Barako": a strong, pungent coffee that is a Filipino favourite.',
                'price_250g' => 34,
                'best_for' => ['french-press', 'drip-filter'],
            ],
            [
                'name' => 'Tungkal Peatland',
                'type' => BeanType::Liberica,
                'origin' => 'Jambi, Indonesia',
                'roast_level' => 'medium',
                'flavour' => ['jackfruit', 'honey', 'woody'],
                'description' => 'A rare liberica that thrives on the peat soils of coastal Sumatra.',
                'price_250g' => 38,
                'best_for' => ['pour-over', 'cold-brew'],
            ],

            // Excelsa
            [
                'name' => 'Amadeo Dark Berry',
                'type' => BeanType::Excelsa,
                'origin' => 'Amadeo, Cavite, Philippines',
                'roast_level' => 'medium',
                'flavour' => ['tart cherry', 'dark berry', 'molasses'],
                'description' => 'From the coffee capital of the Philippines: tart, fruity and surprisingly complex.',
                'price_250g' => 40,
                'best_for' => ['pour-over', 'french-press'],
            ],
            [
                'name' => 'Gia Lai Wild Excelsa',
                'type' => BeanType::Excelsa,
                'origin' => 'Gia Lai, Vietnam',
                'roast_level' => 'medium-dark',
                'flavour' => ['tamarind', 'dark fruit', 'roasted'],
                'description' => 'Known locally as "cà phê mít", grown alongside robusta in the Central Highlands.',
                'price_250g' => 35,
                'best_for' => ['vietnamese-phin', 'cold-brew'],
            ],

            // Blend
            [
                'name' => 'Kopitiam Heritage',
                'type' => BeanType::Blend,
                'origin' => 'Malaysia (Robusta & Liberica)',
                'roast_level' => 'dark',
                'flavour' => ['caramelised sugar', 'butter', 'dark chocolate'],
                'description' => 'A tribute to the classic Malaysian coffee shop: rich, bold and best with condensed milk.',
                'price_250g' => 25,
                'best_for' => ['drip-filter', 'vietnamese-phin', 'french-press'],
            ],
            [
                'name' => 'Mocha-Java Voyage',
                'type' => BeanType::Blend,
                'origin' => 'Yemen & Java, Indonesia',
                'roast_level' => 'medium',
                'flavour' => ['wine', 'chocolate', 'earthy'],
                'description' => 'The world\'s oldest coffee blend, pairing winey Yemeni beans with earthy Javanese ones.',
                'price_250g' => 58,
                'best_for' => ['french-press', 'drip-filter', 'pour-over'],
            ],
            [
                'name' => 'Morning Compass',
                'type' => BeanType::Blend,
                'origin' => 'Brazil & Ethiopia',
                'roast_level' => 'medium',
                'flavour' => ['milk chocolate', 'berry', 'caramel'],
                'description' => 'Our everyday espresso blend: chocolatey Brazilian body with a bright Ethiopian lift.',
                'price_250g' => 40,
                'best_for' => ['espresso', 'moka-pot', 'french-press'],
            ],
        ];
    }
}
