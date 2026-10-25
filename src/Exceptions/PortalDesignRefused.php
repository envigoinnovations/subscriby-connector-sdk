<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * The core declined a portal design call, with a stable reason beside the sentence.
 *
 * A connector branches on `$reason` (`project_unknown`, `forbidden`,
 * `invalid_design`, `no_draft`, `section_unknown`) and prints
 * `getMessage()` when it prints anything; the sentence is in the creator's
 * language where the core has one, and for `invalid_design` it names the
 * choice the portal could not draw, so a bot can relay it under the
 * question it asked.
 */
final class PortalDesignRefused extends RuntimeException
{
    /**
     * @param  string  $reason   The stable reason a connector may branch on.
     * @param  string  $message  The sentence.
     */
    private function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @param   string  $reason   The stable reason.
     * @param   string  $message  The sentence.
     * @return  self    The refusal.
     */
    public static function withReason(string $reason, string $message): self
    {
        return new self($reason, $message);
    }

    /**
     * @param   string  $projectId  The ref's id.
     * @return  self    The refusal for a project the creator may not see.
     */
    public static function projectUnknown(string $projectId): self
    {
        return new self('project_unknown', sprintf('No project the creator may manage has the id "%s".', $projectId));
    }

    /**
     * @param   string  $section  The value as received.
     * @return  self    The refusal for a section the catalogue does not carry.
     */
    public static function sectionUnknown(string $section): self
    {
        return new self('section_unknown', sprintf('The portal design has no section called "%s".', $section));
    }
}
