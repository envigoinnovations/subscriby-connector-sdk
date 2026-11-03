<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\MembershipCancellation;
use Subscriby\Connector\Data\MemberStanding;
use Subscriby\Connector\Data\RecoveryEmailStanding;
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

    /**
     * The member's recovery email as the portal keeps it.
     *
     * The address lets a member sign in to the portal when they lose the
     * platform account they bought through, so a connector's member surface
     * offers it where the platform is the member's only door.
     *
     * @param   InstallationRef             $installation  The project's installation.
     * @param   IdentityRecord              $member        The account asking.
     * @return  RecoveryEmailStanding|null  The address and whether it is verified, or null when the account is nobody's in the project.
     */
    public function recoveryEmailOf(InstallationRef $installation, IdentityRecord $member): ?RecoveryEmailStanding;

    /**
     * Set or change the member's recovery email, which mails the verification link.
     *
     * @param   InstallationRef        $installation  The project's installation.
     * @param   IdentityRecord         $member        The account asking.
     * @param   string                 $email         The address.
     * @return  RecoveryEmailStanding  The address, unverified until the link is clicked.
     *
     * @throws  MembershipRefused  `not_a_member`, or `invalid` when the address fails the portal's checks or another member of the project verified it.
     */
    public function setRecoveryEmail(InstallationRef $installation, IdentityRecord $member, string $email): RecoveryEmailStanding;

    /**
     * Mail the verification link again.
     *
     * @param  InstallationRef  $installation  The project's installation.
     * @param  IdentityRecord   $member        The account asking.
     *
     * @throws  MembershipRefused  `not_a_member`, or `no_email` when none is set or it is verified already.
     */
    public function resendRecoveryEmailVerification(InstallationRef $installation, IdentityRecord $member): void;

    /**
     * Forget the member's recovery email.
     *
     * @param  InstallationRef  $installation  The project's installation.
     * @param  IdentityRecord   $member        The account asking.
     *
     * @throws  MembershipRefused  `not_a_member`, or `no_email` when none is set.
     */
    public function removeRecoveryEmail(InstallationRef $installation, IdentityRecord $member): void;
}
