# Subscriby Connector SDK

Contracts, data objects and the conformance kit for building [Subscriby](https://www.subscriby.net) connectors.

A connector is the piece that teaches Subscriby a platform: how a creator installs it, how members are recognised, which places it can gate, how access is granted and withdrawn, how messages reach people. Subscriby's core owns memberships, plans, payments, grants and the dashboards; a connector owns only what its platform makes it keep. The two meet through this package and nothing else.

Every connector Subscriby runs, official or third-party, depends on this SDK and passes its conformance kit.

## Requirements

- PHP 8.5
- Laravel 13 (`illuminate/contracts`, `illuminate/http`, `illuminate/support`)

## Installation

```bash
composer require subscriby/connector-sdk
```

## What a connector is

An ordinary Composer package with three things in it:

1. **`connector.json`** at the package root, the manifest: key, version, the SDK constraint, install mode and scopes, resource kinds, capabilities, messaging limits and pacing, management commands, relay modes, recovery facets, the marketplace listing and the install and settings fields. The SDK ships `connector.schema.json` (JSON Schema draft 2020-12) so editors and CI validate it, and validates the same rules in PHP with every problem listed at once.
2. **A service provider** extending `Subscriby\Connector\ConnectorServiceProvider`. The base class reads the manifest, registers the connector, loads the package's migrations, views, translations, config, routes and schedules, applies the inbound middleware and the pacing limiter, and binds the ports.
3. **A `Connector`** (`Subscriby\Connector\Contracts\Connector`) that binds its ports through the `ConnectorRegistrar`.

## Ports

A port is an interface under `Subscriby\Connector\Contracts\Ports`. Seven are bound by every connector; the rest are optional and declared as capabilities in the manifest, and the kit checks that the declared capabilities and the bound ports agree.

| Port | Required | What it does |
| --- | --- | --- |
| `InstallationLifecycle` | yes | Begin, complete, verify, describe and disconnect an installation; start links; the platform's shared installation. |
| `IdentityResolver` | yes | Recognise the account behind an inbound event and describe it. |
| `InboundGateway` | yes | Authenticate the platform's call, decode it into envelopes, key each one for idempotency. |
| `FailureClassifier` | yes | Turn a platform refusal into one of the core's failure kinds. |
| `TextRenderer` | yes | Render the canonical HTML subset the core writes in. |
| `SettingsSchema` | yes | The install and settings fields, declarative, so every client renders the same form. |
| `UiSlots` | yes | The views or components the connector contributes to the core's pages. |
| `SpaceCatalog` | capability | Link and diagnose the places the connector gates. |
| `AccessController` | capability | Grant, revoke, reconcile and announce access; hold early admission. |
| `Messenger` | capability | Send, edit and delete messages and files. |
| `ManagementSurface` | capability | Render the core's management commands in the platform's idiom. |
| `PortalLoginMethod` | capability | Sign a member into the portal through the platform. |
| `RegistersCreators` | capability | Let a creator sign up from inside the platform. |
| `SupportRelay` | capability | Carry support conversations between members and creators. |
| `RecoverySupport` | capability | Probe health, register standbys, mirror, relink, fail over. |
| `ProvidesPaymentMethods` | official only | Native payment providers the platform offers. |
| `DataMigrator` | optional | Move legacy data into the neutral tables and verify it. |

Ports take and return the SDK's data objects (`Subscriby\Connector\Data`) and enums (`Subscriby\Connector\Enums`). Nothing from the application crosses the boundary.

## The Core API

A connector reads and writes the creator's world through `Subscriby\Connector\Core\*` (`Installations`, `Identities`, `Spaces`, `Resources`, `Grants`, `Creators`, `Alerts`, `Recovery`, `PaymentMethods`), which the application binds. Every call authorises the way the dashboard does, so a teammate refused on the web is refused in a bot.

## Testing kit

`Subscriby\Connector\Testing\FakeConnector` is a complete connector that deliberately violates every assumption a platform-specific core path might make: no invite links, no direct messages, short messages, phone-number identities, creator-task grants. Its fakes under `Subscriby\Connector\Testing\Fakes` are the doubles a connector's own tests borrow.

`Subscriby\Connector\Testing\Conformance\ConformanceSuite` runs the rules every connector must pass, from `manifest.file_is_the_source` to `migrations.own_tables_only`; passing it is what "works with Subscriby" means, and what a marketplace review will check.

`Subscriby\Connector\Testing\TestRegistry` is the `ConnectorRegistry` a package's own suite hands the kit. It accepts a connector through the same `Subscriby\Connector\Registry\PortAgreement` the application runs at boot, so the kit runs with no application behind it and refuses exactly what production would refuse.

## Data rules

A connector creates only tables prefixed with its key, never alters a core table, points foreign keys inward only (`project_id`, `installation_id`, `identity_id`, cascading on delete) and ships additive migrations after its first publish. The kit reads the migration sources and fails a `Schema::create` without the prefix before anything runs.

## Documentation

The complete guide, port by port and field by field, lives at [docs.subscriby.net/sdk/building](https://docs.subscriby.net/sdk/building).

## Versioning

Semantic versioning. A manifest's `sdk` constraint (`"sdk": "^1.0"`) is checked against `Subscriby\Connector\Sdk::VERSION` when the application boots, so a package built for a contract the SDK no longer keeps is refused at boot rather than at the first call.

## Source

This repository is a read-only mirror of `packages/subscriby-connector-sdk` in Subscriby's monorepo, published on every release tag. Issues and pull requests are welcome here and are carried over.

## License

MIT. See [LICENSE](LICENSE).
