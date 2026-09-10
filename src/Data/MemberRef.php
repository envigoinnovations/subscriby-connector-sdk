<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A project's member (a subscriber), as a connector may refer to them.
 */
final readonly class MemberRef
{
    /**
     * @param  string  $id         The member row's UUID.
     * @param  string  $projectId  The project the membership belongs to.
     */
    public function __construct(
        public string $id,
        public string $projectId,
    ) {}
}
