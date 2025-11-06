<?php

namespace App\Services;

use App\Enums\PurchaseRequestStatus;
use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

class StatusTransitionService
{
    public function __construct(
        private readonly ActivityLogger $logger
    ) {}

    /**
     * Autoriza o pedido: define cotação e muda para IN_PROGRESS.
     */
    public function authorizeRequest(
        User $actor,
        PurchaseRequest $purchaseRequest,
        ?float $unitPrice = null,
        ?\Carbon\Carbon $etaDate = null,
        ?string $carrier = null,
        ?string $notes = null
    ): PurchaseRequest {
        $this->authorizeByPolicy($actor, $purchaseRequest);

        if ($purchaseRequest->status !== PurchaseRequestStatus::PENDING) {
            throw new LogicException('Only PENDING requests can be authorized.');
        }

        $old = $purchaseRequest->getOriginal();

        DB::transaction(function () use (
            $purchaseRequest, $unitPrice, $etaDate, $carrier, $notes, $actor, $old
        ): void {
            $purchaseRequest->fill([
                'unit_price' => $unitPrice,
                'eta_date'   => $etaDate,
                'carrier'    => $carrier,
                'notes'      => $notes ?? $purchaseRequest->notes,
                'status'     => PurchaseRequestStatus::IN_PROGRESS,
            ])->save();

            $this->logger->log(
                purchaseRequest: $purchaseRequest,
                actor: $actor,
                action: 'STATUS_CHANGED',
                oldValues: ['status' => $old['status'] ?? null],
                newValues: ['status' => $purchaseRequest->status->value],
                message: 'Request authorized and moved to IN_PROGRESS.'
            );
        });

        return $purchaseRequest->refresh();
    }

    /**
     * Conclui o pedido: muda para COMPLETED.
     */
    public function complete(
        User $actor,
        PurchaseRequest $purchaseRequest,
        ?string $message = null
    ): PurchaseRequest {
        $this->authorizeByPolicy($actor, $purchaseRequest);

        if ($purchaseRequest->status !== PurchaseRequestStatus::IN_PROGRESS) {
            throw new LogicException('Only IN_PROGRESS requests can be completed.');
        }

        $old = $purchaseRequest->getOriginal();

        DB::transaction(function () use ($purchaseRequest, $actor, $old, $message): void {
            $purchaseRequest->update(['status' => PurchaseRequestStatus::COMPLETED]);

            $this->logger->log(
                purchaseRequest: $purchaseRequest,
                actor: $actor,
                action: 'STATUS_CHANGED',
                oldValues: ['status' => $old['status'] ?? null],
                newValues: ['status' => $purchaseRequest->status->value],
                message: $message ?? 'Request completed.'
            );
        });

        return $purchaseRequest->refresh();
    }

    /**
     * Cancela o pedido: muda para CANCELED com motivo obrigatório.
     */
    public function cancel(
        User $actor,
        PurchaseRequest $purchaseRequest,
        string $reason
    ): PurchaseRequest {
        $this->authorizeByPolicy($actor, $purchaseRequest);

        if ($purchaseRequest->status !== PurchaseRequestStatus::PENDING) {
            throw new LogicException('Only PENDING requests can be canceled.');
        }

        if (trim($reason) === '') {
            throw new InvalidArgumentException('Cancellation reason is required.');
        }

        $old = $purchaseRequest->getOriginal();

        DB::transaction(function () use ($purchaseRequest, $actor, $old, $reason): void {
            $purchaseRequest->update([
                'status' => PurchaseRequestStatus::CANCELED,
                'cancellation_reason' => $reason,
            ]);

            $this->logger->log(
                purchaseRequest: $purchaseRequest,
                actor: $actor,
                action: 'CANCELED',
                oldValues: ['status' => $old['status'] ?? null],
                newValues: [
                    'status' => $purchaseRequest->status->value,
                    'cancellation_reason' => $purchaseRequest->cancellation_reason,
                ],
                message: 'Request canceled with reason.'
            );
        });

        return $purchaseRequest->refresh();
    }

    /**
     * Checa a policy changeStatus para o ator e a PR.
     * @throws AuthorizationException
     */
    private function authorizeByPolicy(User $actor, PurchaseRequest $purchaseRequest): void
    {
        Gate::forUser($actor)->authorize('changeStatus', $purchaseRequest);
    }
}
