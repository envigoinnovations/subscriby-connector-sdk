<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A commitment an approved partner adds after their application, on a declared audience or on a new one.
 *
 * Either the key of an audience the application declared ({@see PartnerAudienceOption})
 * or the three fields of a new audience: the channel's value, the public
 * address and the reach band's value. The core checks the kind is not held
 * yet, that the audience fits the kind and sits above the reach floor, and
 * starts the proof clock the day the promise is added.
 */
final readonly class PartnerCommitmentDraft
{
    /**
     * @param  string       $kind         The kind's value ({@see PartnerCommitmentOption::$kind}).
     * @param  string|null  $audienceKey  The declared audience's key, or null when a new one is described.
     * @param  string|null  $channel      A new audience's channel value; null with a declared audience.
     * @param  string|null  $url          A new audience's public address; null with a declared audience.
     * @param  string|null  $reach        A new audience's reach band value; null with a declared audience.
     */
    public function __construct(
        public string $kind,
        public ?string $audienceKey = null,
        public ?string $channel = null,
        public ?string $url = null,
        public ?string $reach = null,
    ) {}

    /**
     * @return  bool  True when the draft describes an audience not on the application.
     */
    public function describesNewAudience(): bool
    {
        return $this->audienceKey === null;
    }
}
