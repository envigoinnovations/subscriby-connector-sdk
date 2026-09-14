# Changelog

All notable changes to `subscriby/connector-sdk` are listed here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package follows [Semantic Versioning](https://semver.org/).

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
