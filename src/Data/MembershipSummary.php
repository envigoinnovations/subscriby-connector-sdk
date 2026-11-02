<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\PlanKind;

/**
 * One live membership a member holds, as a connector shows it.
 */
final readonly class MembershipSummary
{
    /**
     * @param  string                  $id         The subscription's UUID.
     * @param  string                  $planId     The plan's UUID.
     * @param  string                  $planName   The plan's name.
     * @param  PlanKind                $kind       What kind of plan it is.
     * @param  string                  $status     The status word, translated, as the portal prints it.
     * @param  string                  $price      What it costs, formatted, with the billing cycle for a recurring plan.
     * @param  DateTimeImmutable|null  $renewsAt   The next billing date, or null when it does not renew (a one-time plan, a pass, a canceled subscription).
     * @param  DateTimeImmutable|null  $endsAt     When access ends, when a date is known.
     * @param  bool                    $canceled   Whether the member canceled, keeping access until it ends.
     * @param  bool                    $trialing   Whether it is still in its trial.
     * @param  list<string>            $resources  The titles of the places the plan grants.
     * @param  list<GrantSummary>      $grants     The live grants on it: a `pending` bearer link is a door the member still has to walk through.
     * @param  PassStanding|null       $pass       Where the pass stands, for a pass plan.
     */
    public function __construct(
        public string $id,
        public string $planId,
        public string $planName,
        public PlanKind $kind,
        public string $status,
        public string $price,
        public ?DateTimeImmutable $renewsAt,
        public ?DateTimeImmutable $endsAt,
        public bool $canceled,
        public bool $trialing,
        public array $resources,
        public array $grants,
        public ?PassStanding $pass = null,
    ) {}

    /**
     * @return  bool  True while the membership will bill again.
     */
    public function isRenewing(): bool
    {
        return $this->renewsAt !== null && ! $this->canceled;
    }
}
