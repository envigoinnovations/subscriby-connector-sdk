# Changelog

All notable changes to `subscriby/connector-sdk` are listed here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package follows [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [1.6.0] - 2026-10-10

### Added
- `Core\PartnerProgram`, the platform's Partner Program as a connector's creator surface shows it: `summaryFor(CreatorRef)` (the standing with the programme in the core's words: the pitch, the terms, the rate broken down, the code and the links, the tallies, the balance, how the partner is paid and what stands in the way of being paid), `referrals()`, `rewards()` and `payouts()` paged, `commitments()`, `submitProof()`, `commitmentOptions()`, `audiencesFor()` and `addCommitment()` (a commitment added after the application on a declared audience or a new one), `payoutOptions()`, `updatePayoutDetails()` and `acceptTerms()`; every refusal is `PartnerRefused` with a stable reason. The application itself stays the dashboard's.
- The DTOs the port speaks: `PartnerSummary`, `PartnerStats`, `PartnerBalanceSummary`, `PartnerLink`, `PartnerReferralRecord`/`Page`, `PartnerRewardRecord`/`Page`, `PartnerPayoutRecord`/`Page`, `PartnerCommitmentRecord`, `PartnerCommitmentOptions`, `PartnerCommitmentOption`, `PartnerChannelOption`, `PartnerReachOption`, `PartnerAudienceOption`, `PartnerCommitmentDraft`, `PartnerPayoutOptions`, `PartnerRailOption`, `PartnerPayoutDraft`, `PartnerBankDraft`; the enums `PartnerStanding` and `PartnerAttention`.
- `CreatorRegistration::$partnerCode`: a partner code the sign-up conversation was opened with (a `partner_<code>` deep link), recorded by the core as a referral from the connector.
- `ManagementCommand::PartnerProgram`, the catalogue entry a connector declares when its creator surface shows the programme.
- `Sdk::VERSION` is `1.6.0`; `^1.0` remains the constraint every connector declares.

## [1.5.0] - 2026-10-08

### Added
- `Core\ReferralManagement`, the Referral Program as a connector's creator surface runs it, the other half of `Core\Referrals`: `options(CreatorRef, ProjectRef)` (whether the owner's plan includes the programme and coupon codes, the pickable coupons, plans and currencies, and the limits the core validates against), `programOf()` (every setting typed, the state, the core's words for the reward, the commission period, the friend's reward and the terms, the portal address and the overview's six figures), `saveProgram(…, ReferralProgramDraft)` (the first save creates, every later save changes only what the draft names, null clears), `setProgramActive()`, `deleteProgram()`, `affiliates(…, ?AffiliateStatus, page, perPage)`, `affiliate()`, `approveAffiliate()`, `suspendAffiliate()`, `searchMembers()`, `enrolMember()` and `recordPayout(…, ReferralPayoutDraft)`. Every call names the creator and the project by ref and the core authorises as the dashboard does for that creator on that project. Typed as `ReferralProgramDetails`, `ReferralProgramStats`, `ReferralProgramOptions`, `ReferralProgramDraft`, `ReferralOption`, `AffiliateRecord`, `AffiliateBalanceLine`, `AffiliatePage`, `ReferralPayoutDraft` and `ReferralPayoutRecord`, with `ReferralRewardKind`, `ReferralCommissionType` and `ReferralFriendRewardKind`; refusals leave as `ReferralRefused` with a stable `reason` (`project_unknown`, `forbidden`, `not_entitled`, `program_missing`, `affiliate_unknown`, `member_unknown`, `invalid_settings`, `programme_owes_balances`, `payout_exceeds_balance`, and the join refusals).
- `ManagementCommand::ReferralProgramManage`, the catalogue entry a connector declares when its creator surface runs the programme.
- `Sdk::VERSION` is `1.5.0`; `^1.0` remains the constraint every connector declares.

## [1.4.0] - 2026-10-08

### Added
- `Core\Referrals`, the Referral Program as a connector's member surface offers it: `programFor(InstallationRef)` (the live programme in the core's own words: what a referrer earns, the one-sentence offer, the terms one sentence a line exactly as the core's own approval and change notices state them, what a friend is told on arrival, the attribution window, the creator's terms), `affiliateFor(InstallationRef, IdentityRecord)` (a member's standing with their tallies and what they are owed per currency, live or not), `join()`, `capture(…, string $code, ReferralSource)` for the two doors a connector has (its own deep link carrying the code, and a code typed at the checkout) and `links()` (the connector's deep link and the portal's, labelled). Every call names the project through the installation and the member through the account that is talking, as `Core\Support` does. Typed as `ReferralProgramSummary`, `AffiliateSummary`, `AffiliateEarnings`, `ReferralLink` and `ReferralCapture`, with `ReferralSource` and `AffiliateStatus`; refusals leave as `ReferralRefused` with a stable `reason`.
- `MemberCommand::Referrals`, the button the core puts on a notice to open a member's Refer & Earn screen.
- `Sdk::VERSION` is `1.4.0`; `^1.0` remains the constraint every connector declares.

## [1.3.0] - 2026-09-29

### Added
- The optional `listing.seo` block: `title` (at most 70 characters), `description` (at most 160), `h1`, an optional `h1_sub`, `sections` of `heading` and `body`, and `faq` entries of `q` and `a`. A connector that carries it turns its public page on the marketing site into a search landing page that leads with the platform's name, which the site's own copy never does; the text may use the site's tokens (`:app`, `:gateways`, `:webhook_events`, `:api_resources`, `:mcp_tools`), filled at render time. Typed as `ListingSeo`, `ListingSeoSection` and `ListingSeoQuestion` on `Listing::$seo`; the loader refuses an over-long title or description and any unknown key. A connector without the block keeps the generic page.
- `Sdk::VERSION` is `1.3.0`; `^1.0` remains the constraint every connector declares.

## [1.2.0] - 2026-09-26

The contract release of the first connector: the port that existed only to carry its legacy rows across is gone. It leaves in a minor because no published connector could have bound it (it carried the first connector's own rows, and the first connector is never published), so `^1.0` stays the constraint every connector declares.

### Removed
- The optional `DataMigrator` port with its `BackfillOptions`, `BackfillReport`, `VerificationReport` and `VerificationCheck` data objects, and the conformance rule `migration.reports_for_connector`. They carried the first connector's legacy rows into the neutral tables for the 5.0.0 release; the contract release that drops those rows has no use for them, and no other connector ever had legacy rows to move.

## [1.1.0] - 2026-09-22

### Changed

- The SDK requires PHP 8.5 (`"php": "^8.5"`), the version Subscriby's core now runs. A connector that must stay on PHP 8.4 pins `subscriby/connector-sdk` to `~1.0.0`.

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
