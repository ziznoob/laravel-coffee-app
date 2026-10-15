<?php

namespace Database\Seeders;

use App\Models\BrewMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrewMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            'Espresso',
            'Pour Over',
            'French Press',
            'Drip / Filter',
            'Cold Brew',
            'Moka Pot',
            'Vietnamese Phin',
        ];

        foreach ($methods as $name) {
            BrewMethod::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
