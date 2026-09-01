<?php

namespace App\Enums;

enum DetectionCandidateStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}
