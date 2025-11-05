<?php

namespace App\Enums;

enum PurchaseRequestStatus: string
{
    case PENDING = 'PENDING';
    case PROCESSING = 'IN_PROGRESS';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
}
