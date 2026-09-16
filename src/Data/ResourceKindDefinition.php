<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\GrantMode;
use Subscriby\Connector\Enums\PlanKind;
use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * One kind of place a connector can gate, as the manifest declares it.
 *
 * The labels are what the dashboard and the portal show for the kind, so the
 * core never has to know that a `supergroup` is a Telegram word. The grant mode
 * says how access to such a place is given, the early-admission flag says
 * whether a dated grant can be pre-issued and held until its window opens, and
 * the plan kinds say which shapes of plan a place of the kind can be sold
 * under, so a connector's card tells a creator honestly what they can sell.
 */
final readonly class ResourceKindDefinition
{
    /**
     * The shapes of plan a place of this kind can be sold under, in the SDK enum's order.
     *
     * @var  list<PlanKind>
     */
    public array $planKinds;

    /**
     * @param  string               $kind                        The kind within the connector, a lower-case identifier.
     * @param  string               $label                       The creator-facing name.
     * @param  string               $portalLabel                 The name a buyer sees on a plan card.
     * @param  string               $icon                        The Heroicon name the dashboard draws for it.
     * @param  GrantMode            $grantMode                   How access to a place of this kind is given.
     * @param  bool                 $supportsEarlyAdmissionHold  Whether a dated grant can be held until its window opens.
     * @param  bool                 $mirrorable                  Whether a standby place of this kind can receive a live copy of every post; content is mirrored, conversation is not.
     * @param  string|null          $upgradesFrom                The kind of the same connector a place of this kind can be an upgrade of, so a resource sold as that kind may move onto one of this kind in a recovery; null when none.
     * @param  list<PlanKind>|null  $planKinds                   The shapes of plan a place of this kind can be sold under; null for every shape.
     *
     * @throws  InvalidManifest  When the kind is not a lower-case identifier, a label is empty, the kind claims to upgrade from itself, the plan kinds are empty, a series is sold where a pass is not, or early admission is held for a kind that sells no passes.
     */
    public function __construct(
        public string $kind,
        public string $label,
        public string $portalLabel,
        public string $icon,
        public GrantMode $grantMode,
        public bool $supportsEarlyAdmissionHold = false,
        public bool $mirrorable = false,
        public ?string $upgradesFrom = null,
        ?array $planKinds = null,
    ) {
        if (preg_match('/^[a-z][a-z0-9_-]*$/', $kind) !== 1) {
            throw InvalidManifest::because('unknown', sprintf('resource kind "%s" must be a lower-case identifier', $kind));
        }

        if (trim($label) === '' || trim($portalLabel) === '') {
            throw InvalidManifest::because('unknown', sprintf('resource kind "%s" needs a label and a portal label', $kind));
        }

        if ($upgradesFrom !== null && (preg_match('/^[a-z][a-z0-9_-]*$/', $upgradesFrom) !== 1 || $upgradesFrom === $kind)) {
            throw InvalidManifest::because('unknown', sprintf('resource kind "%s" must upgrade from another lower-case kind identifier', $kind));
        }

        $this->planKinds = self::orderedPlanKinds($kind, $planKinds ?? PlanKind::cases(), $supportsEarlyAdmissionHold);
    }

    /**
     * @param   PlanKind  $planKind  The shape asked about.
     * @return  bool      True when a place of this kind can be sold under it.
     */
    public function sells(PlanKind $planKind): bool
    {
        return in_array($planKind, $this->planKinds, true);
    }

    /**
     * Check the declared plan kinds and put them in the enum's order, so two manifests never list the same set differently.
     *
     * @param   string          $kind       The resource kind, for messages.
     * @param   list<PlanKind>  $planKinds  The shapes as declared.
     * @param   bool            $holds      Whether the kind holds early admission.
     * @return  list<PlanKind>  The shapes, deduplicated and ordered.
     *
     * @throws  InvalidManifest  When nothing is sold, a series is sold without passes, or early admission is held with no passes to hold.
     */
    private static function orderedPlanKinds(string $kind, array $planKinds, bool $holds): array
    {
        $ordered = array_values(array_filter(PlanKind::cases(), static fn (PlanKind $candidate): bool => in_array($candidate, $planKinds, true)));

        if ($ordered === []) {
            throw InvalidManifest::because('unknown', sprintf('resource kind "%s" must sell at least one plan kind', $kind));
        }

        if (in_array(PlanKind::PassSeries, $ordered, true) && ! in_array(PlanKind::Pass, $ordered, true)) {
            throw InvalidManifest::because('unknown', sprintf('resource kind "%s" sells a pass series, so it must sell passes too', $kind));
        }

        if ($holds && ! in_array(PlanKind::Pass, $ordered, true)) {
            throw InvalidManifest::because('unknown', sprintf('resource kind "%s" holds early admission, so it must sell passes', $kind));
        }

        return $ordered;
    }
}
