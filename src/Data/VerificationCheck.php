<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One assertion a data migration makes about the neutral tables against the legacy ones.
 *
 * Expected and actual are kept as values rather than folded into a boolean,
 * because "installations: expected 412, actual 411" tells the operator
 * holding the deploy what to look for and "failed" does not.
 */
final readonly class VerificationCheck
{
    /**
     * @param  string           $name      A stable machine word, such as `installations_match_bots`.
     * @param  bool             $passed    Whether the assertion held.
     * @param  int|string|null  $expected  What the legacy side says.
     * @param  int|string|null  $actual    What the neutral side says.
     * @param  string           $detail    A sentence for the operator, or the first offending ids.
     */
    public function __construct(
        public string $name,
        public bool $passed,
        public int|string|null $expected = null,
        public int|string|null $actual = null,
        public string $detail = '',
    ) {}

    /**
     * A check that compares two counts.
     *
     * @param   string  $name      The machine word.
     * @param   int     $expected  The legacy count.
     * @param   int     $actual    The neutral count.
     * @param   string  $detail    A sentence for the operator.
     * @return  self    Passed when the counts agree.
     */
    public static function counts(string $name, int $expected, int $actual, string $detail = ''): self
    {
        return new self($name, $expected === $actual, $expected, $actual, $detail);
    }

    /**
     * A check that lists the rows that broke it.
     *
     * @param   string        $name       The machine word.
     * @param   list<string>  $offenders  The ids that failed, empty when the check passed.
     * @param   string        $detail     What the offenders are.
     * @return  self          Passed when nothing offended.
     */
    public static function offenders(string $name, array $offenders, string $detail = ''): self
    {
        return new self($name, $offenders === [], 0, count($offenders), $offenders === [] ? $detail : trim($detail.' '.implode(', ', array_slice($offenders, 0, 10))));
    }
}
