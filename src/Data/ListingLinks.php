<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The "More info" links a directory listing shows.
 */
final readonly class ListingLinks
{
    /**
     * @param  string|null  $documentation  Where the connector is documented.
     * @param  string|null  $support        Where a creator asks for help with it.
     * @param  string|null  $privacy        The vendor's privacy policy.
     * @param  string|null  $terms          The vendor's terms.
     * @param  string|null  $homepage       The vendor's site.
     */
    public function __construct(
        public ?string $documentation = null,
        public ?string $support = null,
        public ?string $privacy = null,
        public ?string $terms = null,
        public ?string $homepage = null,
    ) {}
}
