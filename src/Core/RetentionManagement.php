<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Data\RetentionSettingsDetails;
use Subscriby\Connector\Data\RetentionSettingsDraft;
use Subscriby\Connector\Exceptions\RetentionRefused;

/**
 * A project's Revenue Recovery settings, as a connector's creator surface reads and edits them.
 *
 * The other half of {@see Retention}: the creator decides how many days a
 * failed renewal keeps access, whether a cancelling member is asked why,
 * which of the three offers the cancellation screen makes, and whether the
 * daily digest is sent. Every call names the creator and the project by
 * ref and the core authorises as the dashboard does for that creator on
 * that project; the offers gate on the owner's plan, which the details
 * answer so a connector hides what a save would refuse.
 */
interface RetentionManagement
{
    /**
     * The settings as they stand, with the coupons a creator may pick for an offer.
     *
     * @param   CreatorRef                $creator  The creator talking.
     * @param   ProjectRef                $project  The project.
     * @return  RetentionSettingsDetails  Every setting, the ladder in words and the pickable coupons.
     *
     * @throws  RetentionRefused  `project_unknown` when the creator may not manage the project.
     */
    public function settingsOf(CreatorRef $creator, ProjectRef $project): RetentionSettingsDetails;

    /**
     * Change what the draft names and keep the rest.
     *
     * @param   CreatorRef                $creator  The creator talking.
     * @param   ProjectRef                $project  The project.
     * @param   RetentionSettingsDraft    $draft    The fields to change.
     * @return  RetentionSettingsDetails  The settings as written.
     *
     * @throws  RetentionRefused  `project_unknown`, `forbidden` when the creator may not change the project's subscriptions, `invalid_settings` for a window outside the bounds, or `coupon_unknown` for a coupon that is not the project's.
     */
    public function saveSettings(CreatorRef $creator, ProjectRef $project, RetentionSettingsDraft $draft): RetentionSettingsDetails;
}
