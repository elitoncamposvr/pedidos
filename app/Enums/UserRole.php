<?php

namespace App\Enums;

enum UserRole: string
{
    case SELLER = 'SELLER';
    case MANAGER = 'MANAGER';
    case STOCKIST = 'STOCKIST';
    case SUPERVISOR = 'SUPERVISOR';

    public function labelPt(): string
    {
        return match ($this) {
            self::SELLER => __('Vendedor'),
            self::MANAGER => __('Gerente'),
            self::STOCKIST => __('Estoquista'),
            self::SUPERVISOR => __('Supervisor'),
        };
    }

}
