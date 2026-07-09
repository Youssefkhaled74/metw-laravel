<?php

namespace App\Enum;

enum RepresentativeStatus: string
{
    case INCOMPLETE = 'incomplete';
    case PENDING_REVIEW = 'pending_review';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case ACTIVE = 'active';
    case REJECTED = 'rejected';
    case SUSPENDED = 'suspended';
    case INACTIVE = 'inactive';
}
