<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\GrantMode;
use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * One kind of place a connector can gate, as the manifest declares it.
 *
 * The labels are what the dashboard and the portal show for the kind, so the
 * core never has to know that a `supergroup` is a Telegram word. The grant mode
 * says how access to such a place is given, and the early-admission flag says
 * whether a dated grant can be pre-issued and held until its window opens.
 */
final readonly class ResourceKindDefinition
{
    /**
     * @param  string     $kind                        The kind within the connector, a lower-case identifier.
     * @param  string     $label                       The creator-facing name.
     * @param  string     $portalLabel                 The name a buyer sees on a plan card.
     * @param  string     $icon                        The Heroicon name the dashboard draws for it.
     * @param  GrantMode  $grantMode                   How access to a place of this kind is given.
     * @param  bool       $supportsEarlyAdmissionHold  Whether a dated grant can be held until its window opens.
     *
     * @throws  InvalidManifest  When the kind is not a lower-case identifier or a label is empty.
     */
    public function __construct(
        public string $kind,
        public string $label,
        public string $portalLabel,
        public string $icon,
        public GrantMode $grantMode,
        public bool $supportsEarlyAdmissionHold = false,
    ) {
        if (preg_match('/^[a-z][a-z0-9_-]*$/', $kind) !== 1) {
            throw InvalidManifest::because('unknown', sprintf('resource kind "%s" must be a lower-case identifier', $kind));
        }

        if (trim($label) === '' || trim($portalLabel) === '') {
            throw InvalidManifest::because('unknown', sprintf('resource kind "%s" needs a label and a portal label', $kind));
        }
    }
}
