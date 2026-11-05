<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\AccessCodeRedemption;
use Subscriby\Connector\Data\HostedCheckout;
use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\PlanOffer;
use Subscriby\Connector\Exceptions\AccessCodeRefused;
use Subscriby\Connector\Exceptions\CheckoutRefused;

/**
 * What a project sells, as a connector's member surface offers it.
 *
 * The portal is where a purchase is made; a connector that cannot run a
 * checkout of its own shows the plans and hands the person to the portal's
 * checkout for the one they pick, coupon included. A connector with a
 * checkout screen of its own, where the member picks the rail, may instead
 * start a gateway's hosted checkout for that method and hand them the
 * gateway's page. An access code is the one purchase that completes on the
 * connector, because nothing is paid.
 */
interface Storefront
{
    /**
     * @param   InstallationRef  $installation  The project's installation.
     * @return  string|null      The project's portal, or null while the project has no handle.
     */
    public function portalUrl(InstallationRef $installation): ?string;

    /**
     * The plans the project offers an account, in the storefront's order.
     *
     * The core's offer rule applies: only active, purchasable plans, a
     * newcomer's plans to an account the project has never sold to and a
     * customer's to one it has, a single-use plan to an account that never
     * bought it. An account the project does not know is offered what a
     * newcomer is.
     *
     * @param   InstallationRef      $installation  The project's installation.
     * @param   IdentityRecord|null  $member        The account browsing, or null for a stranger.
     * @return  list<PlanOffer>      The plans, each with the portal checkout that opens it.
     */
    public function plansFor(InstallationRef $installation, ?IdentityRecord $member = null): array;

    /**
     * The portal checkout for one plan, with a coupon applied when the project has it.
     *
     * @param   InstallationRef  $installation  The project's installation.
     * @param   string           $planId        The plan.
     * @param   string|null      $coupon        A coupon code to apply, or null.
     * @return  string|null      The address, or null when the plan is not the project's or the project has no handle.
     */
    public function checkoutUrlFor(InstallationRef $installation, string $planId, ?string $coupon = null): ?string;

    /**
     * Start a gateway's hosted checkout for a member on one of the project's methods.
     *
     * For a connector that runs its own checkout screen and lets the member
     * pick the rail there, as the first connector's bot does. The core runs
     * the action the portal runs for that rail: the eligibility checks, the
     * member's coupon, the parked intent the gateway's return and webhook
     * find, and the pending-payment announcement; the connector only sends
     * the address back. A connector's checkout-started line stays its own
     * call, `Retention::recordCheckoutStart()`, made before this one.
     *
     * @param   InstallationRef  $installation  The project's installation.
     * @param   IdentityRecord   $member        The account buying.
     * @param   string           $planId        The plan.
     * @param   string           $methodId      The project's method on the rail the member picked.
     * @return  HostedCheckout   The gateway's page to send the member to, with the rail's name.
     *
     * @throws  CheckoutRefused  When the installation, the member, the plan or the method is unknown, the rail has no hosted page, the member may not buy now, the coupon cannot be honoured, or the gateway refused.
     */
    public function startHostedCheckout(InstallationRef $installation, IdentityRecord $member, string $planId, string $methodId): HostedCheckout;

    /**
     * Redeem an access code for a member, which opens the membership it stands for.
     *
     * @param   InstallationRef       $installation  The project's installation.
     * @param   IdentityRecord        $member        The account redeeming.
     * @param   string                $code          The code as typed.
     * @return  AccessCodeRedemption  The membership opened.
     *
     * @throws  AccessCodeRefused  When the account is nobody's in the project, or the code is unknown, used, expired, or the member already holds a membership.
     */
    public function redeemAccessCode(InstallationRef $installation, IdentityRecord $member, string $code): AccessCodeRedemption;
}
