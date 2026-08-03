<?php

namespace App\Enum;

enum RequestPathStatus: string
{
    /** Couriers are still being matched for the legs of this path. */
    case MATCHING = 'matching';

    /** Every leg has an auto-approved courier; cost has been computed. */
    case COURIER_CONFIRMED = 'courier_confirmed';

    /** The path + cost were presented to the request submitter. */
    case SUBMITTED_TO_CLIENT = 'submitted_to_client';

    /** The submitter selected this path. */
    case CLIENT_SELECTED = 'client_selected';

    /** Execution of the path has started. */
    case EXECUTING = 'executing';

    /** Execution has finished. */
    case EXECUTED = 'executed';

    /** The path could not be fulfilled (no courier accepted). */
    case FAILED = 'failed';

    /** Path was cancelled (e.g. another path was selected). */
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_map(static fn (self $status) => $status->value, self::cases());
    }
}
