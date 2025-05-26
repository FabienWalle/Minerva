<?php

namespace App\Entity\Enums;

enum BookStatus: string
{
    case AVAILABLE = 'available';
    case BORROWED = 'borrowed';
    case RESERVED = 'reserved';
    case MAINTENANCE = 'maintenance';
}