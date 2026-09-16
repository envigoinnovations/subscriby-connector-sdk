<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\InstallationLifecycle;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\DeliveryFailure;
use Subscriby\Connector\Data\Field;
use Subscriby\Connector\Data\InstallationDraft;
use Subscriby\Connector\Data\InstallationHealth;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\InstallationRequest;
use Subscriby\Connector\Data\InstallationSummary;
use Subscriby\Connector\Enums\DeliveryFailureKind;
use Subscriby\Connector\Enums\FieldType;
use Subscriby\Connector\Enums\InstallationScope;
use Subscriby\Connector\Enums\InstallationState;
use Subscriby\Connector\Exceptions\InstallationRefused;

/**
 * An installation lifecycle that connects in one step by default and remembers what it was asked.
 *
 * Three tokens drive the core's connect paths without a platform: a token of
 * `revoked` verifies as revoked; a token starting `steps-` asks for one more
 * field (a region) before it completes, so the second round of a connect can
 * be exercised; a token starting `oauth-` sends the creator to the fake host
 * and completes only from what the platform hands back on the return leg,
 * minting the real credential into the summary's `meta` as an OAuth
 * connector would.
 */
final class FakeInstallationLifecycle implements InstallationLifecycle
{
    /** The prefix of a token that asks a second round of fields. */
    public const string STEPS_PREFIX = 'steps-';

    /** The prefix of a token that sends the creator to the platform first. */
    public const string OAUTH_PREFIX = 'oauth-';

    /** @var list<InstallationRef> */
    public array $disconnected = [];

    /**
     * @param   InstallationRequest  $request  The creator's answers.
     * @return  InstallationDraft    Complete for a plain token; a region field for a `steps-` token until it is answered; the fake host's authorisation page for an `oauth-` token.
     */
    public function begin(InstallationRequest $request): InstallationDraft
    {
        $token = (string) ($request->fields['token'] ?? '');

        if (str_starts_with($token, self::STEPS_PREFIX) && ($request->fields['region'] ?? '') === '') {
            return new InstallationDraft(
                state: ['asked' => 'region'],
                fields: [new Field('region', FieldType::Select, 'Region', 'Where the fake platform hosts this installation.', true, options: ['eu' => 'Europe', 'us' => 'United States'])],
            );
        }

        if (str_starts_with($token, self::OAUTH_PREFIX)) {
            return new InstallationDraft(
                state: ['flow' => 'oauth'],
                continueUrl: 'https://fake.test/oauth?redirect_uri='.rawurlencode((string) $request->returnUrl).'&state=fake',
            );
        }

        return new InstallationDraft;
    }

    /**
     * @param   InstallationRequest  $request      The original request.
     * @param   InstallationDraft    $draft        The completed draft, carrying the return leg for an OAuth token.
     * @param   CredentialBag        $credentials  The token the creator pasted.
     * @return  InstallationSummary  An identity derived from the token, so two tokens make two installations; from the returned code for an OAuth token, with the minted credential in `meta`.
     */
    public function complete(InstallationRequest $request, InstallationDraft $draft, CredentialBag $credentials): InstallationSummary
    {
        if (($draft->state['flow'] ?? null) === 'oauth') {
            $error = $draft->returned['error'] ?? null;

            if (is_string($error) && $error !== '') {
                throw InstallationRefused::byPlatform('fake', new DeliveryFailure(DeliveryFailureKind::NotPermitted, 'The fake platform refused the authorisation: '.$error, code: $error));
            }

            $code = (string) ($draft->returned['code'] ?? '');

            return new InstallationSummary(
                externalId: 'fake-oauth-'.($code === '' ? 'none' : $code),
                displayName: 'Fake OAuth App',
                handle: 'fakeoauth',
                meta: ['token' => 'oauth-'.$code],
            );
        }

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

    /**
     * @param   InstallationRef  $installation  The installation.
     * @return  string           Its page on the fake host, without a payload.
     */
    public function publicUrl(InstallationRef $installation): string
    {
        return 'https://fake.test/'.($installation->externalId ?? 'fake');
    }

    /**
     * @return  InstallationRef  The fake's shared presence, `fake-platform`, since its manifest declares the platform scope.
     */
    public function platformInstallation(): InstallationRef
    {
        return new InstallationRef(
            id: 'fake-platform',
            connector: 'fake',
            scope: InstallationScope::Platform,
            externalId: 'fake-platform',
            handle: 'fakebot',
        );
    }
}
