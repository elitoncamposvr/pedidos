<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityLogFactory> */
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'purchase_request_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'message',
    ];

    /** @var array<string, string>  */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /** @return BelongsTo<PurchaseRequest,ActivityLog> */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /** @return BelongsTo<User,ActivityLog> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
