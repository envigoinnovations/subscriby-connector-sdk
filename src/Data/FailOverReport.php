<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a failover from a lost place to its standby did.
 */
final readonly class FailOverReport
{
    /**
     * @param  int           $readmitted  Holders re-admitted to the standby.
     * @param  int           $failed      Holders the connector could not re-admit.
     * @param  list<string>  $failures    One line per failure, for the closing report.
     */
    public function __construct(
        public int $readmitted,
        public int $failed,
        public array $failures = [],
    ) {}
}
