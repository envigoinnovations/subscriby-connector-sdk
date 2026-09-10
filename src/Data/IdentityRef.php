<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A person's account on a connector.
 *
 * One identity may be linked to a creator, to a member of one project, or to
 * both; the ref says who the account is on the platform, not who holds it here.
 */
final readonly class IdentityRef
{
    /**
     * @param  string       $id          The identity row's UUID.
     * @param  string       $connector   The connector key.
     * @param  string       $externalId  The platform's id for the account (Telegram user id, Discord snowflake, phone number).
     * @param  string|null  $storageRef  The connector's own row id for it, opaque to the core.
     */
    public function __construct(
        public string $id,
        public string $connector,
        public string $externalId,
        public ?string $storageRef = null,
    ) {}
}
