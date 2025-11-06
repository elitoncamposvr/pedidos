<?php

use App\Enums\PurchaseRequestStatus;
use App\Enums\UserRole;
use App\Models\PurchaseRequest;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(); // Popula Shops e Users
});

it('allows seller to update only pending requests from same shop', function () {
    $seller = User::where('role', UserRole::SELLER)->first();
    $shop = $seller->shop;

    $request = PurchaseRequest::factory()->create([
        'shop_id' => $shop->id,
        'requester_id' => $seller->id,
        'status' => PurchaseRequestStatus::PENDING->value,
    ]);

    // Enquanto estiver pendente e for da mesma loja -> pode editar
    expect($seller->can('update', $request))->toBeTrue();

    // Quando o status mudar para IN_PROGRESS -> não pode mais
    $request->update(['status' => PurchaseRequestStatus::IN_PROGRESS->value]);
    expect($seller->can('update', $request))->toBeFalse();
});

it('allows manager and stockist to change status in their own shops', function () {
    $shopMatriz = Shop::where('name', 'Matriz')->first();
    $shopFilial = Shop::where('name', 'Filial')->first();

    $manager = User::where('role', UserRole::MANAGER)->first(); // Matriz
    $stockist = User::where('role', UserRole::STOCKIST)->first(); // Filial

    $requestMatriz = PurchaseRequest::factory()->create(['shop_id' => $shopMatriz->id]);
    $requestFilial = PurchaseRequest::factory()->create(['shop_id' => $shopFilial->id]);

    // Ambos podem mudar status dos pedidos da sua própria loja
    expect($manager->can('changeStatus', $requestMatriz))->toBeTrue()
        ->and($stockist->can('changeStatus', $requestFilial))->toBeTrue();
});

it('restricts seller from changing status', function () {
    $seller = User::where('role', UserRole::SELLER)->first();
    $req = PurchaseRequest::factory()->create(['shop_id' => $seller->shop_id]);

    expect($seller->can('changeStatus', $req))->toBeFalse();
});

it('grants supervisor full access', function () {
    $supervisor = User::where('role', UserRole::SUPERVISOR)->first();
    $req = PurchaseRequest::factory()->create();

    expect($supervisor->can('update', $req))->toBeTrue()
        ->and($supervisor->can('delete', $req))->toBeTrue()
        ->and($supervisor->can('changeStatus', $req))->toBeTrue();
});
