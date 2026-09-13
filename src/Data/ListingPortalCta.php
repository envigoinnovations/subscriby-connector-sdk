<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The button the member portal shows to open a connector.
 *
 * A member reads the portal in a browser but lives on the platform, so the
 * portal offers one button per connected connector that has somewhere to
 * open ("Open Telegram Bot", "Join the server"). The connector names the
 * button; the core builds its address from the installation's start link and
 * renders it with the connector's icon unless the manifest names another.
 */
final readonly class ListingPortalCta
{
    /**
     * @param  string       $label  The button's text, translated through the package's language files.
     * @param  string|null  $icon   An icon slug; the connector's own icon when null.
     */
    public function __construct(
        public string $label,
        public ?string $icon = null,
    ) {}
}
