<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Conformance;

/**
 * One rule of the conformance kit, and whether a connector met it.
 *
 * The detail names what was found rather than what was expected, because the
 * reader is the connector's author deciding what to change.
 */
final readonly class ConformanceCheck
{
    /**
     * @param  string  $name    A stable dotted name, such as `settings.install_fields`.
     * @param  bool    $passed  Whether the rule held.
     * @param  string  $detail  What was found, empty when the rule held.
     */
    public function __construct(
        public string $name,
        public bool $passed,
        public string $detail = '',
    ) {}

    /**
     * @param   string  $name  The rule.
     * @return  self    A rule that held.
     */
    public static function passed(string $name): self
    {
        return new self($name, true);
    }

    /**
     * @param   string  $name    The rule.
     * @param   string  $detail  What was found.
     * @return  self    A rule that did not hold.
     */
    public static function failed(string $name, string $detail): self
    {
        return new self($name, false, $detail);
    }

    /**
     * A rule decided by a list of offenders.
     *
     * @param   string        $name       The rule.
     * @param   list<string>  $offenders  What broke it, empty when nothing did.
     * @param   string        $detail     What the offenders are.
     * @return  self          Passed when the list is empty.
     */
    public static function offenders(string $name, array $offenders, string $detail): self
    {
        return $offenders === [] ? self::passed($name) : self::failed($name, trim($detail.' '.implode(', ', $offenders)));
    }
}
