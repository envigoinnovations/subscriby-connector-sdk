<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\DeliveryResult;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\Message;
use Subscriby\Connector\Data\Recipient;

/**
 * Sending the core's messages through a connector.
 *
 * Bound by connectors that declare `messaging`. The core checks every message
 * against the manifest's limits before calling, renders nothing itself, and
 * reads the result rather than trusting an HTTP status: platforms refuse with
 * `200 OK` as often as with an exception.
 */
interface Messenger
{
    /**
     * @param   InstallationRef  $installation  The installation sending.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   Recipient        $recipient     Who receives it.
     * @param   Message          $message       The canonical message.
     * @return  DeliveryResult   Delivered with the platform's message id, or the classified failure.
     */
    public function send(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, Message $message): DeliveryResult;

    /**
     * @param   InstallationRef  $installation  The installation sending.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   Recipient        $recipient     Who receives it.
     * @param   string           $url           Where the connector fetches the file from.
     * @param   string|null      $caption       Canonical HTML shown with the file, when the platform allows one.
     * @return  DeliveryResult   Delivered, or the classified failure; a connector whose limits say no files fails with `Configuration`.
     */
    public function sendFile(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, string $url, ?string $caption = null): DeliveryResult;
}
