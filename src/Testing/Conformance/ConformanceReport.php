<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Conformance;

/**
 * Every rule the conformance kit checked against one connector.
 *
 * "Passes the kit" is what marketplace review means, so the report is
 * all-or-nothing: one failed rule fails the connector, and the failures carry
 * enough detail to be fixed without re-running anything by hand.
 */
final readonly class ConformanceReport
{
    /**
     * @param  string                  $connector  The connector key.
     * @param  list<ConformanceCheck>  $checks     Every rule, in the order the kit runs them.
     */
    public function __construct(
        public string $connector,
        public array $checks,
    ) {}

    /**
     * @return  bool  True when every rule held.
     */
    public function passed(): bool
    {
        return $this->failures() === [];
    }

    /**
     * @return  list<ConformanceCheck>  The rules that did not hold.
     */
    public function failures(): array
    {
        return array_values(array_filter($this->checks, fn (ConformanceCheck $check): bool => ! $check->passed));
    }

    /**
     * @return  string  One line per failure, for an assertion message or a review note.
     */
    public function summary(): string
    {
        if ($this->passed()) {
            return sprintf('%s passes the conformance kit (%d checks).', $this->connector, count($this->checks));
        }

        return implode(PHP_EOL, array_map(
            fn (ConformanceCheck $check): string => sprintf('%s: %s — %s', $this->connector, $check->name, $check->detail),
            $this->failures(),
        ));
    }
}
