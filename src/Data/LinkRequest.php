<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\LinkPurpose;

/**
 * What a creator is asked to pick a place for.
 *
 * `subjectId` is what the picked place will be linked to: the project for a
 * new resource, the resource for a standby or a replacement. The connector
 * parks it with its own request handle so the answer the platform sends back
 * can be filed under it, and shows `subjectTitle` in its prompt so the creator
 * knows which thing they are picking a place for.
 */
final readonly class LinkRequest
{
    /**
     * @param  string       $kind          The kind of place wanted, one the manifest declares.
     * @param  LinkPurpose  $purpose       What the place will be used for.
     * @param  string|null  $subjectId     The project or resource the place will be linked to.
     * @param  string|null  $subjectTitle  Its title, for the connector's prompt.
     */
    public function __construct(
        public string $kind,
        public LinkPurpose $purpose,
        public ?string $subjectId = null,
        public ?string $subjectTitle = null,
    ) {}
}
