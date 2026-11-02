<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\MembershipCancellation;
use Subscriby\Connector\Data\MemberStanding;
use Subscriby\Connector\Exceptions\MembershipRefused;

/**
 * What a member holds in a project, as a connector's member surface reads it.
 *
 * A bot's home screen, a status command and a "refresh my access" button
 * all begin with the same question: who is this account in the project the
 * installation serves, and what do they hold right now? The answer comes
 * from the core's own rules (which subscriptions are live, what a pass is
 * doing today, which grants admit them) so every connector's surface says
 * the same thing the portal does.
 */
interface Memberships
{
    /**
     * Where an account stands in the project the installation serves.
     *
     * @param   InstallationRef      $installation  The project's installation the account talks to.
     * @param   IdentityRecord       $member        The account, of which the connector key and the platform's id are read.
     * @return  MemberStanding|null  The member, the project and their live memberships, or null when the account is nobody's in that project.
     */
    public function standingOf(InstallationRef $installation, IdentityRecord $member): ?MemberStanding;

    /**
     * Mint fresh grants for everything the member's live subscriptions entitle them to.
     *
     * @param   InstallationRef  $installation  The project's installation the account talks to.
     * @param   IdentityRecord   $member        The account.
     * @return  int              How many grants were minted; zero for an account that is nobody's in the project.
     */
    public function reissueGrants(InstallationRef $installation, IdentityRecord $member): int;

    /**
     * Cancel the member's live membership, the way the portal's last step does: access runs to the end of what was paid.
     *
     * The retention screen (`Core\Retention`) comes first on a surface that
     * offers one; this is the write after the member has said no to it.
     *
     * @param   InstallationRef         $installation  The project's installation the account talks to.
     * @param   IdentityRecord          $member        The account.
     * @return  MembershipCancellation  The membership cancelled, and when the access they keep ends.
     *
     * @throws  MembershipRefused  When the account is nobody's in the project, holds no live membership, or holds one that cannot be cancelled.
     */
    public function cancel(InstallationRef $installation, IdentityRecord $member): MembershipCancellation;
}
