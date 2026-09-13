<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The words the marketing site borrows from a connector.
 *
 * Core marketing copy is written once with placeholders (":platform",
 * ":places", ":native_payment") and rendered from the connectors that are
 * available, so a home page, a vertical page or a legal clause names a
 * platform only because a connector for it exists. Everything here is
 * lower-case prose except the proper nouns the platform itself capitalises.
 */
final readonly class ListingMarketing
{
    /**
     * @param  string       $audience       Who the connector serves, as marketing names them ("Telegram communities").
     * @param  string       $place          One gated place ("channel").
     * @param  string       $places         The gated places as a list ("channels, groups and supergroups").
     * @param  string       $installation   What a project installs ("bot").
     * @param  string       $identity       A person's account on the platform ("Telegram account").
     * @param  string|null  $nativePayment  The platform's own payment method, when it has one ("Telegram Stars").
     */
    public function __construct(
        public string $audience,
        public string $place,
        public string $places,
        public string $installation,
        public string $identity,
        public ?string $nativePayment = null,
    ) {}
}
