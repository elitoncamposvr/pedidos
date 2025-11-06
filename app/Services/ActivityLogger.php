<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\PurchaseRequest;
use App\Models\User;

class ActivityLogger
{
    public function log(
        PurchaseRequest $purchaseRequest,
        User $actor,
        string $action,
        array $oldValues = [],
        array $newValues = [],
        ?string $message = null
    ): ActivityLog {
        /** @var ActivityLog $log */
        $log = ActivityLog::query()->create([
            'purchase_request_id' => $purchaseRequest->id,
            'user_id'             => $actor->id,
            'action'              => $action,
            'old_values'          => $oldValues ?: null,
            'new_values'          => $newValues ?: null,
            'message'             => $message,
        ]);

        return $log;
    }
}
