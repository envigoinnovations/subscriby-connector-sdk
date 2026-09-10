<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\Field;
use Subscriby\Connector\Data\InstallationRef;

/**
 * The install and settings forms, declared as data.
 *
 * Required of every connector. The dashboard, the REST API and the mobile
 * apps all render these fields, so a connector needs no Blade to be
 * installable; a `UiSlots` contribution may dress the same fields up on the
 * web but never replaces them.
 */
interface SettingsSchema
{
    /**
     * @return  list<Field>  What a creator fills in to connect, in display order.
     */
    public function installFields(): array;

    /**
     * @param   InstallationRef|null  $installation  The installation being edited, or null for the defaults.
     * @return  list<Field>           What a creator may change afterwards, with current values.
     */
    public function settingsFields(?InstallationRef $installation): array;
}
