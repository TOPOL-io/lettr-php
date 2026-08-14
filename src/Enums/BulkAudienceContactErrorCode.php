<?php

declare(strict_types=1);

namespace Lettr\Enums;

/**
 * Reason a single row was skipped during a bulk contact create.
 *
 * These are per-row codes reported in the 201 response body — they are not the
 * top-level API {@see ErrorCode} values used for failed requests.
 */
enum BulkAudienceContactErrorCode: string
{
    case MissingEmail = 'missing_email';
    case InvalidEmail = 'invalid_email';
    case InvalidPropertyValue = 'invalid_property_value';
    case UnknownPropertyKey = 'unknown_property_key';
    case UnknownList = 'unknown_list';
    case UnknownTopic = 'unknown_topic';
    case InvalidTopicSubscription = 'invalid_topic_subscription';

    /**
     * Get a human-readable message for the error code.
     */
    public function message(): string
    {
        return match ($this) {
            self::MissingEmail => 'The row has no email address.',
            self::InvalidEmail => 'The email address is not valid.',
            self::InvalidPropertyValue => 'A property value is not valid for its property type.',
            self::UnknownPropertyKey => 'A property key is not defined for this team.',
            self::UnknownList => 'A list id does not exist for this team.',
            self::UnknownTopic => 'A topic id does not exist for this team.',
            self::InvalidTopicSubscription => 'A topic subscription value is not `opt_in` or `opt_out`.',
        };
    }
}
