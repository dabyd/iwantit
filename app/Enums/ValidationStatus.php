<?php

namespace App\Enums;

enum ValidationStatus: string
{
    case Unvalidated = 'unvalidated';
    case Validated = 'validated';
    case Rejected = 'rejected';
}
