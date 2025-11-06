<?php

use App\Enums\PurchaseRequestStatus;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Services\StatusTransitionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(); // Shops + Users
    $this->service = app(StatusTransitionService::class);
});

it('authorizes a pending request and moves to IN_PROGRESS with log', function () {
    $manager = User::where('role', UserRole::MANAGER)->first();
    $pr = PurchaseRequest::factory()->create([
        'shop_id' => $manager->shop_id,
        'status'  => PurchaseRequestStatus::PENDING->value,
        'unit_price' => null,
        'eta_date' => null,
        'carrier' => null,
    ]);

    $updated = $this->service->authorizeRequest(
        actor: $manager,
        purchaseRequest: $pr,
        unitPrice: 123.45,
        etaDate: now()->addDays(3),
        carrier: 'Jadlog',
        notes: 'Cotado com fornecedor X'
    );

    expect($updated->status)->toBe(PurchaseRequestStatus::IN_PROGRESS)
        ->and($updated->unit_price)->toBe('123.45')
        ->and($updated->carrier)->toBe('Jadlog');

    expect(ActivityLog::where('purchase_request_id', $updated->id)
        ->where('action', 'STATUS_CHANGED')->exists())->toBeTrue();
});

it('completes an in-progress request and logs the change', function () {
    $stockist = User::where('role', UserRole::STOCKIST)->first();
    $pr = PurchaseRequest::factory()->create([
        'shop_id' => $stockist->shop_id,
        'status'  => PurchaseRequestStatus::IN_PROGRESS->value,
    ]);

    $updated = $this->service->complete(
        actor: $stockist,
        purchaseRequest: $pr,
        message: 'Chegou e foi conferido.'
    );

    expect($updated->status)->toBe(PurchaseRequestStatus::COMPLETED);

    expect(ActivityLog::where('purchase_request_id', $updated->id)
        ->where('action', 'STATUS_CHANGED')->exists())->toBeTrue();
});

it('cancels a pending request with reason and logs it', function () {
    $manager = User::where('role', UserRole::MANAGER)->first();
    $pr = PurchaseRequest::factory()->create([
        'shop_id' => $manager->shop_id,
        'status'  => PurchaseRequestStatus::PENDING->value,
    ]);

    $updated = $this->service->cancel(
        actor: $manager,
        purchaseRequest: $pr,
        reason: 'Fornecedor sem estoque'
    );

    expect($updated->status)->toBe(PurchaseRequestStatus::CANCELED)
        ->and($updated->cancellation_reason)->toBe('Fornecedor sem estoque');

    expect(ActivityLog::where('purchase_request_id', $updated->id)
        ->where('action', 'CANCELED')->exists())->toBeTrue();
});

it('prevents seller from authorizing requests', function () {
    $seller = User::where('role', UserRole::SELLER)->first();
    $pr = PurchaseRequest::factory()->create([
        'shop_id' => $seller->shop_id,
        'status'  => PurchaseRequestStatus::PENDING->value,
    ]);

    $this->expectException(AuthorizationException::class);

    $this->service->authorizeRequest(
        actor: $seller,
        purchaseRequest: $pr,
        unitPrice: 99.90
    );
});

it('requires reason to cancel', function () {
    $manager = User::where('role', UserRole::MANAGER)->first();
    $pr = PurchaseRequest::factory()->create([
        'shop_id' => $manager->shop_id,
        'status'  => PurchaseRequestStatus::PENDING->value,
    ]);

    $this->expectException(InvalidArgumentException::class);

    $this->service->cancel(
        actor: $manager,
        purchaseRequest: $pr,
        reason: ''
    );
});

it('blocks invalid transitions', function () {
    $manager = User::where('role', UserRole::MANAGER)->first();
    $pr = PurchaseRequest::factory()->create([
        'shop_id' => $manager->shop_id,
        'status'  => PurchaseRequestStatus::COMPLETED->value,
    ]);

    $this->expectException(LogicException::class);
    $this->service->authorizeRequest(actor: $manager, purchaseRequest: $pr);
});
