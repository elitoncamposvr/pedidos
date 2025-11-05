<?php

namespace App\Models;

use App\Enums\PurchaseRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use HasFactory, SoftDeletes;

    /** @var array<int, string>  */
    protected $fillable = [
        'description',
        'notes',
        'item_code',
        'quantity',
        'unit_price',
        'eta_date',
        'carrier',
        'status',
        'customer',
        'requester_id',
        'reference_os',
        'shop_id',
        'cancellation_reason',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'eta_date' => 'date',
        'unit_price' => 'decimal:2',
        'status' => PurchaseRequestStatus::class,
    ];

    /** @return BelongsTo<User, PurchaseRequest> */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /** @return BelongsTo<Shop, PurchaseRequest> */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /** @return HasMany<ActivityLog> */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
