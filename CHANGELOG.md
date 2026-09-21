# Changelog

All notable changes to `subscriby/connector-sdk` are listed here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package follows [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [1.0.1] - 2026-09-22

### Added

- `Core\Creators::isEmailTaken(string $email): bool`: whether an address already belongs to a creator, compared without regard to case, so a sign-up conversation refuses a taken address at the step it was typed instead of at `register()`.

## [1.0.0] - 2026-09-19

The first public release, the contract every Subscriby connector is built against.

### Added

- `connector.json` as the manifest, with `connector.schema.json` (JSON Schema draft 2020-12) and the PHP reader that lists every problem at once.
- The `Connector` and `ConnectorRegistrar` contracts and the `ConnectorRegistry` the application binds.
- Seven required ports: `InstallationLifecycle`, `IdentityResolver`, `InboundGateway`, `FailureClassifier`, `TextRenderer`, `SettingsSchema`, `UiSlots`.
- Capability ports: `SpaceCatalog`, `AccessController`, `Messenger`, `ManagementSurface`, `PortalLoginMethod`, `RegistersCreators`, `SupportRelay`, `RecoverySupport`, `ProvidesPaymentMethods` (official connectors only), and the optional `DataMigrator`.
- The Core API contracts (`Installations`, `Identities`, `Spaces`, `Grants`, `Creators`, `Alerts`, `Recovery`, `PaymentMethods`) a connector calls instead of the application.
- Data objects, refs and enums for everything that crosses the boundary, including the canonical HTML message model with URL, callback and copy actions.
- `ConnectorServiceProvider`, the base provider that wires a package in from its manifest.
- The testing kit: `FakeConnector`, its port fakes, and the `ConformanceSuite` with every rule a connector must pass.
- `Sdk::VERSION` and `Sdk::satisfies()`, the constraint check the registry runs at boot.
- The `settings`, `broadcast_hints`, `access_code_redemption_hint`, `resource_badge` and `grant_action` slots are rendered: each installation's settings note on the Configuration tab (`installation`), a hint per live installation under the broadcast editor (`installation`) and under "Where members redeem a code" in the access-code generator (`installation`), a badge beside each gated resource in the resources list (`resource`, `space`), and an action beside each place in a subscription's detail (`resource`, `space`, `joined`).
- The `creator_login_method` slot is rendered: the creator's sign-in and sign-up pages show one contribution per available connector that fills it (`<x-connector-slots>`), and `RegistersCreators::signupEntry()` gains its caller, the sign-up page's "Sign up inside … instead" entry for every connector whose platform installation answers a link.
- `CredentialBag::with()` and `InstallationSummary::credentialsFor()`: what a connector puts in a summary's `meta` while connecting is merged into the credentials the core stores, so a secret minted at `complete()` (a webhook signing secret) survives and returns in every bag.
- `Core\Installations::credentials()`: the stored bag of an installation the connector can name, for an inbound gateway that has to verify a signature before the core has handed it anything.
- `Registry\PortRegistrar` and `Registry\PortAgreement`: collecting a connector's ports and checking them against its manifest (the required ports, capability and port in both directions, official-only capabilities, fields declared in the file versus a bound `SettingsSchema`) now live in the SDK, so every registry refuses the same connector in the same words.
- `Testing\TestRegistry` and `Testing\PassthroughTranslator`: a `ConnectorRegistry` for a package's own suite, so the conformance kit runs without an application. The official, available and disabled lists are constructor arguments, and manifest-declared form labels come back as written.
- `Core\Resources`, `Data\ResourceRef` and `Exceptions\ResourceRefused`: a connector creates the resource that sells a place a creator picked (`create()`, idempotent on the project and the place, authorised as the dashboard's own "add a resource" is, bound to the space and announced through `project.resource.linked`), and reads resources by id, by place and by project.
- `Core\Recovery::registerStandby()` and `replaceSpace()` with `Exceptions\RecoveryRefused`: a standby or a replacement place is filed through the same actions the dashboard runs, and the core's refusal arrives with its stable reason and the translated sentence for the creator.
- `FakeMessenger::assertNotSentTo($externalId, ?$containing)`: the negative of `assertSentTo()`, for a core path that must pass an account over.
- Multi-step and OAuth connects are followed by the core: `InstallationRequest` gains `returnUrl` (the core's URL the platform sends the creator back to; an OAuth `begin()` builds its authorisation URL on it) and `state` (what the previous draft parked, when the creator answers a further round of fields), and `InstallationDraft` gains `returned` with `withReturned()` (the return leg's query string, handed to `complete()`). A draft with `fields` grows the dashboard's form and `begin()` runs again with every answer so far; a draft with a `continueUrl` is parked for a quarter of an hour under a one-time token and completed from the return leg. `FakeInstallationLifecycle` drives both: a `steps-` token asks a `region` first, an `oauth-` token sends the creator to the fake host and completes from `returned['code']`, minting the credential into `meta`.
- `Core\Support`, with `Data\InboundSupportMessage`, `Data\InboundSupportAttachment`, `Data\IngestedSupportMessage`, `Data\SupportMessageRef`, `Data\SupportConversationSummary`, `Data\SupportRelayTarget`, `Enums\SupportMessageKind`, `Enums\SupportReplySource` and `Exceptions\SupportRefused`: a connector files a member's message into the project's inbox (`acceptsMessages()`, `ingest()`, which records the account, finds or creates the member as a lead, folds an edit or a replay into the row it already has, and hands back the acknowledgement due), records a creator's answer written on the platform (`reply()`, authorised as the dashboard's reply is, null author for the owner), finds the thread behind a relay ping's button, a native reply to a relay message, or a thread in the relay space (`conversationForReply()`, `conversationForRelayMessage()`, `conversationForThread()`), and records or forgets the project's relay space (`relayTarget()`, `linkRelaySpace()`, `unlinkRelaySpace()`). The inbox now files a thread under any connector key and stamps messages with the connector-neutral sources `connector_chat`, `connector_relay_dm` and `connector_relay_group`.
- `IdentityResolver::deliveryTarget()`: every connector says whether an installation can write to an account right now (Telegram: the person opened this bot; Discord: direct messages are open), so the core passes over an account it cannot reach for one it can, before a wasted call. The kit's new rule `identity.answers_reachability` checks that an account the connector never saw is answered with null or a `Recipient`, never a throw. `FakeIdentityResolver` refuses the `blocked-` accounts the fake messenger refuses.
