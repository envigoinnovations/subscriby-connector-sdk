<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * A resource the core would not create or act on for a connector.
 *
 * Raised by `Core\Resources::create()` and by the recovery writes that name a
 * resource, so a connector reads one refusal whatever the core's own
 * exception was. The reason is a stable key a connector can branch on; the
 * message is for the developer, never for a creator.
 */
final class ResourceRefused extends RuntimeException
{
    /**
     * @param  string  $reason   The stable key: `kind_outside_place`, `place_unknown`, `project_unknown`, `resource_unknown` or `not_permitted`.
     * @param  string  $message  What was refused, in one sentence.
     */
    private function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @param   string  $kind       The stored kind that was offered.
     * @param   string  $connector  The connector the place belongs to.
     * @return  self    The exception.
     */
    public static function kindOutsidePlace(string $kind, string $connector): self
    {
        return new self('kind_outside_place', sprintf('The kind "%s" cannot sell a place on the "%s" connector; a resource is sold under its place\'s connector and never as the manual perk.', $kind, $connector));
    }

    /**
     * @param   string  $spaceId  The space id that named nothing.
     * @return  self    The exception.
     */
    public static function placeUnknown(string $spaceId): self
    {
        return new self('place_unknown', sprintf('No space has the id "%s"; record the place through Core\Spaces before selling it.', $spaceId));
    }

    /**
     * @param   string  $projectId  The project id that named nothing.
     * @return  self    The exception.
     */
    public static function projectUnknown(string $projectId): self
    {
        return new self('project_unknown', sprintf('No project has the id "%s".', $projectId));
    }

    /**
     * @param   string  $resourceId  The resource id that named nothing.
     * @return  self    The exception.
     */
    public static function resourceUnknown(string $resourceId): self
    {
        return new self('resource_unknown', sprintf('No resource has the id "%s".', $resourceId));
    }

    /**
     * @return  self  The exception.
     */
    public static function notPermitted(): self
    {
        return new self('not_permitted', 'The creator this request runs as may not add resources to the project, or nobody is acting.');
    }
}
