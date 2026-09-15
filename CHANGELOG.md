# Changelog

All notable changes to `subscriby/connector-sdk` are listed here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package follows [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- `CredentialBag::with()` and `InstallationSummary::credentialsFor()`: what a connector puts in a summary's `meta` while connecting is merged into the credentials the core stores, so a secret minted at `complete()` (a webhook signing secret) survives and returns in every bag.
- `Core\Installations::credentials()`: the stored bag of an installation the connector can name, for an inbound gateway that has to verify a signature before the core has handed it anything.
- `Registry\PortRegistrar` and `Registry\PortAgreement`: collecting a connector's ports and checking them against its manifest (the required ports, capability and port in both directions, official-only capabilities, fields declared in the file versus a bound `SettingsSchema`) now live in the SDK, so every registry refuses the same connector in the same words.
- `Testing\TestRegistry` and `Testing\PassthroughTranslator`: a `ConnectorRegistry` for a package's own suite, so the conformance kit runs without an application. The official, available and disabled lists are constructor arguments, and manifest-declared form labels come back as written.

## [1.0.0] - 2026-09-15

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
