<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the platform says an installation is, once connected.
 *
 * The name and handle come from the platform rather than the creator, so the
 * dashboard always shows the bot or app as its members see it.
 */
final readonly class InstallationSummary
{
    /**
     * @param  string                $externalId   The platform's id for the installation.
     * @param  string                $displayName  The name the platform reports.
     * @param  string|null           $handle       The username or handle, when the platform has one.
     * @param  string|null           $avatarUrl    A picture, when the platform has one.
     * @param  array<string, mixed>  $meta         Anything else the connector wants kept with the installation.
     */
    public function __construct(
        public string $externalId,
        public string $displayName,
        public ?string $handle = null,
        public ?string $avatarUrl = null,
        public array $meta = [],
    ) {}
}
