<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * How a connector's sign-in option appears on the portal's login sheet.
 */
final readonly class PortalLoginButton
{
    /**
     * @param  string  $label  What the button says, such as "Continue with Telegram".
     * @param  string  $icon   The icon the sheet draws next to it.
     */
    public function __construct(
        public string $label,
        public string $icon,
    ) {}
}
