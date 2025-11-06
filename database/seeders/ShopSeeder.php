<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{

      public function run(): void
    {
        Shop::query()->delete();

        Shop::create([
            'name' => 'Matriz',
            'city' => 'Vila Rica',
            'state' => 'MT',
        ]);

        Shop::create([
            'name' => 'Filial',
            'city' => 'Santana do Araguaia',
            'state' => 'PA',
        ]);
    }
}
