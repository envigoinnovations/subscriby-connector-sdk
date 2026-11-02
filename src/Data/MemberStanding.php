<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Who an account is in a project, and what they hold there right now.
 */
final readonly class MemberStanding
{
    /**
     * @param  MemberRef                $member       The member the account belongs to.
     * @param  string                   $name         The member's display name.
     * @param  ProjectRef               $project      The project.
     * @param  string                   $projectName  The project's name.
     * @param  string|null              $portalUrl    The project's portal, or null while it has no handle.
     * @param  list<MembershipSummary>  $memberships  The live memberships, oldest first; empty for a member who holds nothing right now.
     */
    public function __construct(
        public MemberRef $member,
        public string $name,
        public ProjectRef $project,
        public string $projectName,
        public ?string $portalUrl,
        public array $memberships,
    ) {}

    /**
     * @return  bool  True when the member holds at least one live membership.
     */
    public function holdsAccess(): bool
    {
        return $this->memberships !== [];
    }
}
