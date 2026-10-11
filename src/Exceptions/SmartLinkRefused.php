<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * The core declined a Smart Links call, with a stable reason beside the sentence.
 *
 * A connector branches on `$reason` (`installation_unknown`, `code_unknown`,
 * `project_unknown`, `link_unknown`, `forbidden`) and prints `getMessage()`
 * when it prints anything; an arrival through a bad code is silent by
 * design, so the member lands on the same screen as everyone else.
 */
final class SmartLinkRefused extends RuntimeException
{
    /**
     * @param  string  $reason   The stable reason a connector may branch on.
     * @param  string  $message  The sentence, in the person's language where the core has one.
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
     * @param   string  $installationId  The ref's id.
     * @return  self    The refusal for a ref that names no project.
     */
    public static function installationUnknown(string $installationId): self
    {
        return new self('installation_unknown', sprintf('No project installation has the id "%s"; a platform-wide installation carries no Smart Links.', $installationId));
    }

    /**
     * @param   string  $code  The code as received.
     * @return  self    The refusal for a code no live link of the project carries.
     */
    public static function codeUnknown(string $code): self
    {
        return new self('code_unknown', sprintf('No live Smart Link of this project has the code "%s".', $code));
    }

    /**
     * @param   string  $linkId  The id as received.
     * @return  self    The refusal for an id no link has.
     */
    public static function linkUnknown(string $linkId): self
    {
        return new self('link_unknown', sprintf('No Smart Link has the id "%s".', $linkId));
    }
}
