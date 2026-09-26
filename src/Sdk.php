<?php

declare(strict_types=1);

namespace Subscriby\Connector;

use Subscriby\Connector\Contracts\Ports\FailureClassifier;
use Subscriby\Connector\Contracts\Ports\IdentityResolver;
use Subscriby\Connector\Contracts\Ports\InboundGateway;
use Subscriby\Connector\Contracts\Ports\InstallationLifecycle;
use Subscriby\Connector\Contracts\Ports\SettingsSchema;
use Subscriby\Connector\Contracts\Ports\TextRenderer;
use Subscriby\Connector\Contracts\Ports\UiSlots;

/**
 * Facts about the SDK itself that a connector or the application asks for.
 *
 * The version is what a manifest's `sdk` constraint is checked against, so a
 * package built for a contract this SDK no longer keeps is refused at boot
 * rather than at the first call. The constraint syntax is the small subset
 * Composer users write by hand (`^0.1`, `~1.2`, `>=1.0`, `1.0.0`, `*`, joined
 * by `||`), read here without a dependency because the SDK is what every
 * connector already depends on.
 */
final class Sdk
{
    /** The SDK's own version, bumped with every release. */
    public const string VERSION = '1.2.0';

    /**
     * The ports every connector binds whatever it can do.
     *
     * @var  list<class-string>
     */
    public const array REQUIRED_PORTS = [
        InstallationLifecycle::class,
        IdentityResolver::class,
        InboundGateway::class,
        FailureClassifier::class,
        TextRenderer::class,
        SettingsSchema::class,
        UiSlots::class,
    ];

    /**
     * Whether a version satisfies a constraint.
     *
     * @param   string       $constraint  The manifest's `sdk` constraint.
     * @param   string|null  $version     The version to test, the SDK's own by default.
     * @return  bool         True when any `||` alternative holds and, within one, every space-separated part holds.
     */
    public static function satisfies(string $constraint, ?string $version = null): bool
    {
        $version = self::normalise($version ?? self::VERSION);

        if ($version === null) {
            return false;
        }

        foreach (preg_split('/\s*\|\|\s*/', trim($constraint)) ?: [] as $alternative) {
            $parts = preg_split('/[\s,]+/', trim($alternative), -1, PREG_SPLIT_NO_EMPTY) ?: [];

            if ($parts !== [] && array_all($parts, fn (string $part): bool => self::satisfiesPart($part, $version))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param   string                         $part     One constraint without `||`.
     * @param   array{0: int, 1: int, 2: int}  $version  The version to test, as three numbers.
     * @return  bool                           True when the part holds.
     */
    private static function satisfiesPart(string $part, array $version): bool
    {
        if ($part === '*') {
            return true;
        }

        if (preg_match('/^(\^|~|>=|<=|>|<|=)?\s*(\d+)(?:\.(\d+))?(?:\.(\d+))?$/', $part, $matches) !== 1) {
            return false;
        }

        $operator = $matches[1];
        $given = [(int) $matches[2], (int) ($matches[3] ?? 0), (int) ($matches[4] ?? 0)];
        $precision = isset($matches[4]) ? 3 : (isset($matches[3]) ? 2 : 1);

        return match ($operator) {
            '^' => self::compare($version, $given) >= 0 && self::compare($version, self::caretCeiling($given)) < 0,
            '~' => self::compare($version, $given) >= 0 && self::compare($version, self::tildeCeiling($given, $precision)) < 0,
            '>=' => self::compare($version, $given) >= 0,
            '>' => self::compare($version, $given) > 0,
            '<=' => self::compare($version, $given) <= 0,
            '<' => self::compare($version, $given) < 0,
            default => $precision === 3 ? self::compare($version, $given) === 0 : (self::compare($version, $given) >= 0 && self::compare($version, self::wildcardCeiling($given, $precision)) < 0),
        };
    }

    /**
     * The first version a bare partial constraint excludes: `1.2` means `1.2.*`, `1` means `1.*`.
     *
     * @param   array{0: int, 1: int, 2: int}  $given      The constraint's version.
     * @param   int                            $precision  How many parts were written.
     * @return  array{0: int, 1: int, 2: int}  The ceiling.
     */
    private static function wildcardCeiling(array $given, int $precision): array
    {
        return $precision >= 2 ? [$given[0], $given[1] + 1, 0] : [$given[0] + 1, 0, 0];
    }

    /**
     * The first version a caret constraint excludes: the next major, or the next minor below 1.0.
     *
     * @param   array{0: int, 1: int, 2: int}  $given  The constraint's version.
     * @return  array{0: int, 1: int, 2: int}  The ceiling.
     */
    private static function caretCeiling(array $given): array
    {
        if ($given[0] > 0) {
            return [$given[0] + 1, 0, 0];
        }

        if ($given[1] > 0) {
            return [0, $given[1] + 1, 0];
        }

        return [0, 0, $given[2] + 1];
    }

    /**
     * The first version a tilde constraint excludes: the next of the second-most-specific part given.
     *
     * @param   array{0: int, 1: int, 2: int}  $given      The constraint's version.
     * @param   int                            $precision  How many parts were written.
     * @return  array{0: int, 1: int, 2: int}  The ceiling.
     */
    private static function tildeCeiling(array $given, int $precision): array
    {
        return $precision >= 3 ? [$given[0], $given[1] + 1, 0] : [$given[0] + 1, 0, 0];
    }

    /**
     * @param   array{0: int, 1: int, 2: int}  $left   A version.
     * @param   array{0: int, 1: int, 2: int}  $right  Another.
     * @return  int                            Negative, zero or positive as the left is lower, equal or higher.
     */
    private static function compare(array $left, array $right): int
    {
        return $left <=> $right;
    }

    /**
     * @param   string                              $version  A `major.minor.patch` string, pre-release suffixes ignored.
     * @return  array{0: int, 1: int, 2: int}|null  The three numbers, or null when the string is not a version.
     */
    private static function normalise(string $version): ?array
    {
        if (preg_match('/^v?(\d+)\.(\d+)\.(\d+)/', trim($version), $matches) !== 1) {
            return null;
        }

        return [(int) $matches[1], (int) $matches[2], (int) $matches[3]];
    }
}
