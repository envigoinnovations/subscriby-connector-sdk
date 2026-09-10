<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use InvalidArgumentException;

/**
 * How a connector's data migration should run.
 *
 * A dry run reads everything and writes nothing, so the report can be read on
 * paper against a production dump before the deploy; the chunk size bounds
 * memory on the tables that hold every member.
 */
final readonly class BackfillOptions
{
    /**
     * @param  bool  $dryRun     Report what would be written without writing.
     * @param  int   $chunkSize  Rows read per chunk.
     *
     * @throws  InvalidArgumentException  When the chunk size is not positive.
     */
    public function __construct(
        public bool $dryRun = false,
        public int $chunkSize = 500,
    ) {
        if ($chunkSize < 1) {
            throw new InvalidArgumentException('The backfill chunk size must be a positive integer.');
        }
    }
}
