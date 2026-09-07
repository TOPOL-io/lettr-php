<?php

declare(strict_types=1);

namespace Lettr\Enums;

/**
 * Which module a template belongs to.
 *
 * A template is filed into a folder of its own module, and the two modules do
 * not mix: only `Campaign` templates can be picked by the campaign builder,
 * and only `Transactional` ones can be sent as single emails.
 */
enum TemplatePurpose: string
{
    case Transactional = 'transactional';
    case Campaign = 'campaign';

    public function label(): string
    {
        return match ($this) {
            self::Transactional => 'Transactional',
            self::Campaign => 'Campaign',
        };
    }
}
