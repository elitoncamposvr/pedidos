<?php

use App\Enums\UserRole;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('db:seed');
});

it('seeds two shops', function () {
    expect(Shop::count())->toBe(2)
        ->and(Shop::pluck('name')->toArray())->toContain('Matriz', 'Filial');
});

it('seeds four users with correct roles', function () {
    expect(User::count())->toBe(4)
        ->and(User::where('role', UserRole::SELLER)->exists())->toBeTrue()
        ->and(User::where('role', UserRole::MANAGER)->exists())->toBeTrue()
        ->and(User::where('role', UserRole::STOCKIST)->exists())->toBeTrue()
        ->and(User::where('role', UserRole::SUPERVISOR)->exists())->toBeTrue();
});

it('ensures users are linked to shops', function () {
    $user = User::first();
    expect($user->shop)->not->toBeNull();
});
