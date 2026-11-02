<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationAction;
use Subscriby\Connector\Data\InstallationActionResult;
use Subscriby\Connector\Data\InstallationRef;

/**
 * The things a creator can have an installation do on its platform, beyond gating: offered as data, run on request.
 *
 * Bound by connectors that declare `installation_actions`. An action is a
 * creator-facing verb the core cannot know in advance (build a members area
 * in a fresh server, rename the bot in it), declared with its label and
 * fields so the dashboard, the REST API, the MCP server, the automation
 * surfaces and the Copilot all offer it the same way, and run through one
 * door with the core's authorisation already done.
 */
interface InstallationActions
{
    /**
     * @param   InstallationRef           $installation  The installation the actions are for.
     * @return  list<InstallationAction>  The actions offered right now, in display order.
     */
    public function actions(InstallationRef $installation): array;

    /**
     * Run one action.
     *
     * @param   InstallationRef           $installation  The installation acting.
     * @param   CredentialBag             $credentials   Its secrets.
     * @param   string                    $action        The action's key, one `actions()` listed.
     * @param   array<string, mixed>      $fields        The creator's answers to the action's fields, validated against their rules.
     * @return  InstallationActionResult  What happened, in words the creator reads; an unknown key is refused here, never thrown.
     */
    public function run(InstallationRef $installation, CredentialBag $credentials, string $action, array $fields): InstallationActionResult;
}
