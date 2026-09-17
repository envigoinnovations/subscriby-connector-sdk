<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\HandshakeCompletion;
use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\IdentityRef;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\MemberRef;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Enums\IdentityLinkSource;
use Subscriby\Connector\Enums\IdentityPurpose;
use Subscriby\Connector\Exceptions\HandshakeRefused;

/**
 * The identity rows and who they belong to, as a connector may read and write them.
 *
 * A connector says which account the platform is talking about; the core
 * decides which creator or member holds it. Recording an identity and linking
 * it are separate calls because the same account can be a creator in one
 * place and a member in another, and because a purchase may know the account
 * before anybody has proven who holds it. A handshake is the two-sided proof
 * that joins them: the core opens it, the connector completes it with the
 * account that answered, and the core writes the link.
 */
interface Identities
{
    /**
     * @param   string            $id  The identity row's UUID.
     * @return  IdentityRef|null  The identity, or null when none has that id.
     */
    public function find(string $id): ?IdentityRef;

    /**
     * @param   string            $connector       The connector key.
     * @param   string|null       $installationId  The installation the account was seen through, or null for a platform-wide id.
     * @param   string            $externalId      The platform's id for the account.
     * @return  IdentityRef|null  The identity, or null when the account is unknown.
     */
    public function findByExternalId(string $connector, ?string $installationId, string $externalId): ?IdentityRef;

    /**
     * Write an identity, or refresh the one already known.
     *
     * Idempotent on the connector key, the installation and the platform id.
     *
     * @param   IdentityRecord  $record  What the platform says about the account.
     * @return  IdentityRef     The row, new or updated.
     */
    public function record(IdentityRecord $record): IdentityRef;

    /**
     * Say that a creator holds an identity.
     *
     * Idempotent: linking the same identity to the same creator again changes
     * nothing. An identity already held by another creator is refused by the
     * core, because one account signs in as one person.
     *
     * @param  IdentityRef         $identity  The account.
     * @param  CreatorRef          $creator   Who holds it.
     * @param  IdentityPurpose     $purpose   Primary or backup.
     * @param  IdentityLinkSource  $source    How the link was proven.
     */
    public function linkCreator(IdentityRef $identity, CreatorRef $creator, IdentityPurpose $purpose, IdentityLinkSource $source): void;

    /**
     * Say that a member of a project holds an identity.
     *
     * Idempotent per project: the same identity linked to the same member
     * again changes nothing, and the link is per project because a person is
     * a different member in each.
     *
     * @param  IdentityRef         $identity   The account.
     * @param  MemberRef           $member     Who holds it, in which project.
     * @param  IdentityLinkSource  $source     How the link was proven.
     * @param  bool                $preferred  Whether the member wants to be reached here first.
     */
    public function linkMember(IdentityRef $identity, MemberRef $member, IdentityLinkSource $source, bool $preferred = false): void;

    /**
     * Whether a token names a handshake the core ever issued, in whatever state it is now.
     *
     * A connector's typed-code path asks this before claiming a message: a
     * word from the code alphabet is a code only if the core minted it, so a
     * wizard answer that happens to look like one is left to its wizard.
     *
     * @param   string  $token  The token as typed or carried by the deep link.
     * @return  bool    True when a handshake carries it.
     */
    public function isHandshakeToken(string $token): bool;

    /**
     * Complete a handshake with the account that answered it.
     *
     * The connector proves possession (the account opened the link or typed
     * the code in a private conversation with the shared installation); the
     * core decides what the handshake was for and writes the link. A creator
     * link makes the account the creator's primary identity on the connector;
     * a portal sign-in makes (or finds) the account's member in the handshake's
     * project and tells the connector where the person goes next. A connector
     * that can say which installation heard the account passes it, so a
     * project-bound handshake completes only through that project's own
     * installation.
     *
     * @param   string                $token     The token as typed or carried by the deep link.
     * @param   IdentityRecord        $identity  The account that answered, as the connector describes it.
     * @param   InstallationRef|null  $seenBy    The installation that heard the account, when the connector can say.
     * @return  HandshakeCompletion   What was completed, whose account it now is, and where they go next.
     *
     * @throws  HandshakeRefused  When the token names no pending handshake, the account already belongs to another person, another project's installation heard it, or the purpose is not completed through this call.
     */
    public function completeHandshake(string $token, IdentityRecord $identity, ?InstallationRef $seenBy = null): HandshakeCompletion;

    /**
     * @param   IdentityRef      $identity  The account.
     * @return  CreatorRef|null  The creator who holds it, or null when no creator does.
     */
    public function findCreator(IdentityRef $identity): ?CreatorRef;

    /**
     * @param   IdentityRef     $identity  The account.
     * @param   ProjectRef      $project   The project asked about.
     * @return  MemberRef|null  The member who holds it there, or null when none does.
     */
    public function findMember(IdentityRef $identity, ProjectRef $project): ?MemberRef;

    /**
     * Every account a creator holds, so a connector can address them on any of its own rows.
     *
     * @param   CreatorRef         $creator  The creator.
     * @return  list<IdentityRef>  Their accounts, primary before backup, by connector; empty when they hold none.
     */
    public function listForCreator(CreatorRef $creator): array;
}
