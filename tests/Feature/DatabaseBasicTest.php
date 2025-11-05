<?php

use App\Enums\PurchaseRequestStatus;
use App\Enums\UserRole;
use App\Models\PurchaseRequest;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('has core tables', function () {
    expect(Schema::hasTable('shops'))->toBeTrue()
        ->and(Schema::hasTable('users'))->toBeTrue()
        ->and(Schema::hasTable('purchase_requests'))->toBeTrue()
        ->and(Schema::hasTable('activity_logs'))->toBeTrue();
});

it('creates a purchase request with enum casts working', function () {
    $shop = Shop::factory()->create();
    $user = User::factory()->create(['shop_id' => $shop->id, 'role' => UserRole::SELLER]);

    /** @var PurchaseRequest $pr */
    $pr = PurchaseRequest::factory()->create([
        'shop_id' => $shop->id,
        'requester_id' => $user->id,
    ]);

    expect($pr->status)->toBeInstanceOf(PurchaseRequestStatus::class)
        ->and($pr->status)->toBe(PurchaseRequestStatus::PENDING);
});
