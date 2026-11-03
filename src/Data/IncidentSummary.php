<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * An open recovery incident of a creator's, as a connector's surface reads it.
 *
 * The issue is the core's word for what was lost (`account`, `bot` or
 * `resources`), the ids name what it concerns, and the reason is the
 * connector's own wording of why, when the connector words that code.
 */
final readonly class IncidentSummary
{
    /**
     * @param  string                 $id              The incident id.
     * @param  string                 $issue           What was lost: `account`, `bot` or `resources`.
     * @param  DateTimeImmutable      $detectedAt      When the core detected it.
     * @param  string|null            $projectId       The project it concerns, or null for the creator's account.
     * @param  string|null            $resourceId      The resource it concerns, when one.
     * @param  string|null            $installationId  The installation it concerns, when one.
     * @param  HealthReasonText|null  $reason          The connector's label and remedy for the reason code, when it words that code.
     */
    public function __construct(
        public string $id,
        public string $issue,
        public DateTimeImmutable $detectedAt,
        public ?string $projectId = null,
        public ?string $resourceId = null,
        public ?string $installationId = null,
        public ?HealthReasonText $reason = null,
    ) {}
}
