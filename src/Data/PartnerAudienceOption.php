<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * An audience the partner declared on their application that a kind can be done on above the reach floor.
 *
 * The key is the audience's position on the application, the one a
 * {@see PartnerCommitmentDraft} names; the label is the channel, the
 * address and the reach in one line.
 */
final readonly class PartnerAudienceOption
{
    /**
     * @param  string  $key    The audience's position on the application.
     * @param  string  $label  Channel, address and reach, in one line.
     */
    public function __construct(
        public string $key,
        public string $label,
    ) {}
}
