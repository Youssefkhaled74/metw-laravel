<?php

namespace App\Enum;

enum ShipmentRequestStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';

    /** Couriers are being matched for the request paths. */
    case MATCHING = 'matching';

    /** One or more paths succeeded and were presented to the submitter. */
    case AWAITING_PATH_SELECTION = 'awaiting_path_selection';

    /** Submitter (User) selected a path; advance payment awaits admin confirmation. */
    case AWAITING_ADVANCE = 'awaiting_advance';

    /** Execution of the selected path has started. */
    case EXECUTING = 'executing';

    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

    /** No courier accepted any path – closed with the failure notification text. */
    case FAILED_UNAVAILABLE = 'failed_unavailable';

    public static function values(): array
    {
        return array_map(
            static fn (self $status) => $status->value,
            self::cases()
        );
    }
}
