<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums\Concerns;

/**
 * The value helpers every backed enum in the SDK carries.
 *
 * The SDK cannot depend on the application's `App\Enums\Concerns\HasEnumHelpers`,
 * so it carries its own copy of the two derived-from-cases helpers; deriving
 * them from `self::cases()` keeps a renamed enum from returning another class's
 * values, which is the trap the application's trait exists to close.
 */
trait EnumHelpers
{
    /**
     * Every case's backing value, in declaration order.
     *
     * @return  array<int, string>
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Alias of {@see toArray()}.
     *
     * @return  array<int, string>
     */
    public static function values(): array
    {
        return self::toArray();
    }
}
