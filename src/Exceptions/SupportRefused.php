<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * A support write the core would not make for a connector.
 *
 * Raised by `Core\Support`'s writes, so a connector reads one refusal
 * whatever the core's own exception was. The reason is a stable key a
 * connector can branch on; the message is for the developer, never for a
 * creator or a member.
 */
final class SupportRefused extends RuntimeException
{
    /**
     * @param  string  $reason   The stable key: `conversation_unknown`, `project_unknown`, `installation_unknown`, `space_unknown` or `not_permitted`.
     * @param  string  $message  What was refused, in one sentence.
     */
    private function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @param   string  $conversationId  The thread id that named nothing.
     * @return  self    The exception.
     */
    public static function conversationUnknown(string $conversationId): self
    {
        return new self('conversation_unknown', sprintf('No support conversation has the id "%s".', $conversationId));
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
     * @param   string  $installationId  The installation id that names no project's installation.
     * @return  self    The exception.
     */
    public static function installationUnknown(string $installationId): self
    {
        return new self('installation_unknown', sprintf('No project installation has the id "%s"; a platform-wide installation carries no inbox.', $installationId));
    }

    /**
     * @param   string  $spaceId  The space id that named nothing the core can point the relay at.
     * @return  self    The exception.
     */
    public static function spaceUnknown(string $spaceId): self
    {
        return new self('space_unknown', sprintf('No space has the id "%s" and the ref names no row of your own; record the place through Core\Spaces before linking it.', $spaceId));
    }

    /**
     * @return  self  The exception.
     */
    public static function notPermitted(): self
    {
        return new self('not_permitted', 'The creator this request names may not answer on the project, or nobody may act on it.');
    }
}
