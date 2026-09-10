<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The kinds of field a connector's install and settings forms are built from.
 *
 * Declarative on purpose: the dashboard, the REST API and the mobile apps all
 * render the same fields, so a connector needs no Blade to be installable.
 */
enum FieldType: string
{
    use EnumHelpers;

    case Text = 'text';
    case Secret = 'secret';
    case Select = 'select';
    case Toggle = 'toggle';
    case Instructions = 'instructions';
    case Link = 'link';

    /**
     * @return  bool  True when the field carries a value the creator types, rather than copy or a link.
     */
    public function isInput(): bool
    {
        return match ($this) {
            self::Text, self::Secret, self::Select, self::Toggle => true,
            self::Instructions, self::Link => false,
        };
    }
}
