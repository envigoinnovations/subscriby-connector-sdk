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
     * @param  array<string, mixed>  $meta         Anything the connector minted while connecting and needs back later (a webhook signing secret): the core stores it encrypted beside the credentials and hands it back in every `CredentialBag`, and `Core\Installations::credentials()` reads it before any port is called.
     * @param  string|null           $storageRef   The connector's own row for the installation, when it keeps one; the core stores it as the installation's `storage_ref`.
     */
    public function __construct(
        public string $externalId,
        public string $displayName,
        public ?string $handle = null,
        public ?string $avatarUrl = null,
        public array $meta = [],
        public ?string $storageRef = null,
    ) {}

    /**
     * The bag the core stores for the installation: what the creator gave, plus what the connector minted.
     *
     * @param   CredentialBag  $given  The credentials the connector was handed to connect with.
     * @return  CredentialBag  The same bag when the summary carries no meta, otherwise a new one with the meta merged in.
     */
    public function credentialsFor(CredentialBag $given): CredentialBag
    {
        return $this->meta === [] ? $given : $given->with($this->meta);
    }
}
