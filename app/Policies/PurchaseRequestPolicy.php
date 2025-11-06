<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\PurchaseRequest;
use App\Models\User;

class PurchaseRequestPolicy
{
    /** Qualquer usuário autenticado pode visualizar pedidos da sua loja */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SELLER,
            UserRole::MANAGER,
            UserRole::STOCKIST,
            UserRole::SUPERVISOR,
        ], true);
    }

    /** Pode ver o pedido se for da mesma loja ou for supervisor */
    public function view(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->role === UserRole::SUPERVISOR
            || $user->shop_id === $purchaseRequest->shop_id;
    }

    /** Pode criar sempre (apenas autenticado) */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SELLER,
            UserRole::MANAGER,
            UserRole::STOCKIST,
            UserRole::SUPERVISOR,
        ], true);
    }

    /** Pode editar se for o dono do pedido e estiver PENDING, ou for superior */
    public function update(User $user, PurchaseRequest $purchaseRequest): bool
    {
        if ($user->role === UserRole::SUPERVISOR) {
            return true;
        }

        if (in_array($user->role, [UserRole::MANAGER, UserRole::STOCKIST], true)
            && $user->shop_id === $purchaseRequest->shop_id) {
            return true;
        }

        return $user->role === UserRole::SELLER
            && $user->shop_id === $purchaseRequest->shop_id
            && $purchaseRequest->status->value === 'PENDING';
    }

    /** Pode excluir se for supervisor ou o próprio vendedor enquanto estiver pendente */
    public function delete(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->role === UserRole::SUPERVISOR
            || ($user->role === UserRole::SELLER
                && $user->shop_id === $purchaseRequest->shop_id
                && $purchaseRequest->status->value === 'PENDING');
    }

    /** Pode autorizar (→ IN_PROGRESS) ou concluir (→ COMPLETED) se for gerente, estoquista ou supervisor */
    public function changeStatus(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return in_array($user->role, [
                UserRole::MANAGER,
                UserRole::STOCKIST,
                UserRole::SUPERVISOR,
            ], true) && $user->shop_id === $purchaseRequest->shop_id;
    }
}
