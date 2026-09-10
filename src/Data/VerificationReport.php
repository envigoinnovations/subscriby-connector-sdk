<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Every assertion a connector's data migration makes, and whether they all held.
 *
 * This is the gate before any contract migration: the command that prints it
 * exits non-zero on a single failure, and the release process reads that exit
 * code, so a report can never be "mostly green".
 */
final readonly class VerificationReport
{
    /**
     * @param  string                   $connector  The connector key.
     * @param  list<VerificationCheck>  $checks     Every assertion, in the order the migration makes them.
     */
    public function __construct(
        public string $connector,
        public array $checks = [],
    ) {}

    /**
     * @return  bool  True when every check held.
     */
    public function passed(): bool
    {
        return $this->failures() === [];
    }

    /**
     * @return  list<VerificationCheck>  The checks that did not hold.
     */
    public function failures(): array
    {
        return array_values(array_filter($this->checks, fn (VerificationCheck $check): bool => ! $check->passed));
    }
}
