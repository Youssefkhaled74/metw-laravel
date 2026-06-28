<?php

namespace App\Enum;

enum RepresentativeStatus: string
{
    case INCOMPLETE = 'incomplete';
    case PENDING_REVIEW = 'pending_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case SUSPENDED = 'suspended';
}
