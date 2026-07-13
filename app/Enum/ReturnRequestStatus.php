<?php

namespace App\Enum;

enum ReturnRequestStatus: string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case RECEIVED_INSPECTING = 'received_inspecting';
    case ACCEPTED_RETURN = 'accepted_return';
    case REJECTED_RETURN = 'rejected_return';
    case COMPLETED = 'completed';
}
