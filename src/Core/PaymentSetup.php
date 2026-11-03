<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\PaymentMethodDetails;
use Subscriby\Connector\Data\PaymentMethodDraft;
use Subscriby\Connector\Data\PaymentProviderKey;
use Subscriby\Connector\Data\PaymentProviderOption;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Enums\PaymentMode;
use Subscriby\Connector\Exceptions\PaymentSetupRefused;

/**
 * A project's payment methods as a connector's creator surface sets them up: the gateways on offer, the methods held, and the two doors in.
 *
 * A gateway is connected one of two ways, and the core says which: by
 * onboarding (the creator is sent to the gateway's own pages through
 * `onboardingUrl()` and comes back connected) or by the keys the gateway
 * issued (`configure()` from a draft the core checks as the dashboard's
 * form does). The gateway list and the fields each needs come from the
 * core, so a connector never names a gateway of its own. Every call but
 * `providers()` takes the `CreatorRef` of the account talking and runs as
 * that creator under the dashboard's rules, refused as `PaymentSetupRefused`
 * with a stable reason otherwise.
 */
interface PaymentSetup
{
    /**
     * @return  list<PaymentProviderOption>  Every gateway the core offers a project, with whether it onboards or which keys it needs.
     */
    public function providers(): array;

    /**
     * @param   CreatorRef                  $creator  The creator talking.
     * @param   ProjectRef                  $project  The project.
     * @return  list<PaymentMethodDetails>  The project's methods, in storage order.
     *
     * @throws  PaymentSetupRefused  `creator_unknown` or `project_unknown`.
     */
    public function listForProject(CreatorRef $creator, ProjectRef $project): array;

    /**
     * Configure a gateway from the keys it issued, checked as the dashboard checks them, stored encrypted, and switched on.
     *
     * @param   CreatorRef            $creator  The creator talking.
     * @param   ProjectRef            $project  The project.
     * @param   PaymentMethodDraft    $draft    The gateway, the mode and the keys.
     * @return  PaymentMethodDetails  The method as written.
     *
     * @throws  PaymentSetupRefused  `creator_unknown`, `project_unknown`, `provider_unknown` (no such gateway, or one that onboards instead), `invalid`, `already_configured` or `not_permitted`.
     */
    public function configure(CreatorRef $creator, ProjectRef $project, PaymentMethodDraft $draft): PaymentMethodDetails;

    /**
     * Where a gateway's own onboarding starts for the project, with the method row made or revived behind it.
     *
     * @param   CreatorRef          $creator   The creator talking.
     * @param   ProjectRef          $project   The project.
     * @param   PaymentProviderKey  $provider  A gateway that onboards.
     * @param   PaymentMode         $mode      Real money or the sandbox.
     * @return  string              The address the creator opens; it is theirs alone and expires.
     *
     * @throws  PaymentSetupRefused  `creator_unknown`, `project_unknown`, `provider_unknown` (no such gateway, or one configured from keys instead), `invalid` or `not_permitted`.
     */
    public function onboardingUrl(CreatorRef $creator, ProjectRef $project, PaymentProviderKey $provider, PaymentMode $mode): string;

    /**
     * @param   CreatorRef            $creator   The creator talking.
     * @param   string                $methodId  The method.
     * @param   bool                  $active    True to offer it at checkout, false to stop.
     * @return  PaymentMethodDetails  The method as written.
     *
     * @throws  PaymentSetupRefused  `creator_unknown`, `method_unknown`, `invalid` (an onboarding never finished cannot be switched on) or `not_permitted`.
     */
    public function setActive(CreatorRef $creator, string $methodId, bool $active): PaymentMethodDetails;

    /**
     * @param  CreatorRef  $creator   The creator talking.
     * @param  string      $methodId  The method.
     *
     * @throws  PaymentSetupRefused  `creator_unknown`, `method_unknown` or `not_permitted`.
     */
    public function delete(CreatorRef $creator, string $methodId): void;
}
