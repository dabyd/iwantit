<?php

namespace App\Enums;

enum Taxonomy: string
{
    case KeyContext = 'key_context';
    case InventoryType = 'inventory_type';
    case Family = 'family';
    case ClearanceFamily = 'clearance_family';
}
