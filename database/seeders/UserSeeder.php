<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->delete();

        $matriz = Shop::where('name', 'Matriz')->firstOrFail();
        $filial = Shop::where('name', 'Filial')->firstOrFail();

        User::create([
            'name' => 'Vendedor Matriz',
            'email' => 'seller@matriz.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SELLER,
            'shop_id' => $matriz->id,
        ]);

        User::create([
            'name' => 'Gerente Matriz',
            'email' => 'manager@matriz.com',
            'password' => Hash::make('password'),
            'role' => UserRole::MANAGER,
            'shop_id' => $matriz->id,
        ]);

        User::create([
            'name' => 'Estoquista Filial',
            'email' => 'stockist@filial.com',
            'password' => Hash::make('password'),
            'role' => UserRole::STOCKIST,
            'shop_id' => $filial->id,
        ]);

        User::create([
            'name' => 'Supervisor Geral',
            'email' => 'supervisor@empresa.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SUPERVISOR,
            'shop_id' => $matriz->id,
        ]);
    }
}
