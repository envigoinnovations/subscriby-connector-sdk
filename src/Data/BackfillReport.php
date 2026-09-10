<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a connector's data migration did, or would do.
 *
 * Counts are per neutral table so the operator can compare them with the
 * legacy row counts by eye; `skipped` counts rows that were already present,
 * which is what makes a second run of an idempotent migration read as `0
 * written, N skipped` rather than as silence.
 */
final readonly class BackfillReport
{
    /**
     * @param  string                 $connector  The connector key.
     * @param  bool                   $dryRun     Whether nothing was written.
     * @param  array<string, int>     $written    Table name to rows written, or that would have been.
     * @param  array<string, int>     $skipped    Table name to rows already present and left alone.
     * @param  list<BackfillAnomaly>  $anomalies  The legacy rows that did not map cleanly.
     */
    public function __construct(
        public string $connector,
        public bool $dryRun,
        public array $written = [],
        public array $skipped = [],
        public array $anomalies = [],
    ) {}

    /**
     * @return  int  Rows written, or that would have been, across every table.
     */
    public function totalWritten(): int
    {
        return array_sum($this->written);
    }

    /**
     * @return  int  Rows already present across every table.
     */
    public function totalSkipped(): int
    {
        return array_sum($this->skipped);
    }

    /**
     * @return  list<string>  Every table the report mentions, sorted.
     */
    public function tables(): array
    {
        $tables = array_unique([...array_keys($this->written), ...array_keys($this->skipped)]);

        sort($tables);

        return $tables;
    }

    /**
     * @return  bool  True when some legacy row did not map cleanly.
     */
    public function hasAnomalies(): bool
    {
        return $this->anomalies !== [];
    }
}
