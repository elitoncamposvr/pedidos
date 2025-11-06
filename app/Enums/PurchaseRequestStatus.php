<?php
namespace App\Enums;

enum PurchaseRequestStatus: string
{
case PENDING = 'PENDING';
case IN_PROGRESS = 'IN_PROGRESS';
case COMPLETED = 'COMPLETED';
case CANCELED = 'CANCELED';
}
