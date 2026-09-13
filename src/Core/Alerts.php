<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\IdentityRef;

/**
 * Where a creator's alerts go, as far as a connector may ask or change it.
 *
 * A connector's admin surface offers the creator one switch for the account it
 * is talking to: whether the core's alerts (a sale, a waiting member, an
 * incident) reach them through that account. The core keeps the destinations
 * and the classes each carries; a connector only ever asks about, or flips,
 * the account in front of it, and the core still mails the critical classes
 * whatever the switch says.
 */
interface Alerts
{
    /**
     * @param   CreatorRef   $creator   The creator.
     * @param   IdentityRef  $identity  One of their accounts on a connector.
     * @return  bool         Whether any class of alert reaches the creator through that account.
     */
    public function receivesAlerts(CreatorRef $creator, IdentityRef $identity): bool;

    /**
     * Route every class of the creator's alerts through the account, or none of them.
     *
     * @param   CreatorRef   $creator   The creator.
     * @param   IdentityRef  $identity  One of their accounts on a connector.
     * @param   bool         $receives  True to carry every class, false to carry none.
     * @return  bool         Whether the switch was written; false when the core knows no such creator, or the account is not theirs.
     */
    public function setReceivesAlerts(CreatorRef $creator, IdentityRef $identity, bool $receives): bool;
}
