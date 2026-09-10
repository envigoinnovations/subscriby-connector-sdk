<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\InstallationLifecycle;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationDraft;
use Subscriby\Connector\Data\InstallationHealth;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\InstallationRequest;
use Subscriby\Connector\Data\InstallationSummary;
use Subscriby\Connector\Enums\InstallationState;

/**
 * An installation lifecycle that connects in one step and remembers what it was asked.
 *
 * A token of `revoked` verifies as revoked, so the core's health paths can be
 * driven without a platform.
 */
final class FakeInstallationLifecycle implements InstallationLifecycle
{
    /** @var list<InstallationRef> */
    public array $disconnected = [];

    /**
     * @param   InstallationRequest  $request  The creator's answers.
     * @return  InstallationDraft    Always complete: the fake needs nothing more.
     */
    public function begin(InstallationRequest $request): InstallationDraft
    {
        return new InstallationDraft;
    }

    /**
     * @param   InstallationRequest  $request      The original request.
     * @param   InstallationDraft    $draft        The completed draft.
     * @param   CredentialBag        $credentials  The token the creator pasted.
     * @return  InstallationSummary  An identity derived from the token, so two tokens make two installations.
     */
    public function complete(InstallationRequest $request, InstallationDraft $draft, CredentialBag $credentials): InstallationSummary
    {
        $token = (string) $credentials->get('token');

        return new InstallationSummary(
            externalId: 'fake-'.hash('crc32b', $token),
            displayName: 'Fake Bot',
            handle: 'fakebot',
        );
    }

    /**
     * @param   InstallationRef     $installation  The installation.
     * @param   CredentialBag       $credentials   Its secrets.
     * @return  InstallationHealth  Revoked for the token `revoked`, healthy otherwise.
     */
    public function verify(InstallationRef $installation, CredentialBag $credentials): InstallationHealth
    {
        if ($credentials->get('token') === 'revoked') {
            return new InstallationHealth(InstallationState::Revoked, 'token_revoked', 'The token was revoked on the platform.', true);
        }

        return InstallationHealth::healthy();
    }

    /**
     * @param  InstallationRef  $installation  The installation.
     * @param  CredentialBag    $credentials   Its secrets.
     */
    public function disconnect(InstallationRef $installation, CredentialBag $credentials): void
    {
        $this->disconnected[] = $installation;
    }

    /**
     * @param   InstallationRef      $installation  The installation.
     * @param   CredentialBag        $credentials   Its secrets.
     * @return  InstallationSummary  The same identity `complete()` reported.
     */
    public function describe(InstallationRef $installation, CredentialBag $credentials): InstallationSummary
    {
        return new InstallationSummary($installation->externalId ?? 'fake-unknown', 'Fake Bot', 'fakebot');
    }

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   string|null      $payload       What the conversation opens on.
     * @return  string           A link on the fake host.
     */
    public function startLink(InstallationRef $installation, ?string $payload = null): string
    {
        return 'https://fake.test/'.($installation->externalId ?? 'fake').'?start='.($payload ?? 'start');
    }
}
