<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * A recovery write the core refused, with the sentence the creator should read.
 *
 * The core refuses a standby or a replacement for reasons of its own (the
 * actor does not own the project, the plan lacks prevention, the place is
 * another kind or already sold, the allowance is spent) and words each
 * refusal for the creator in their language. A connector relays the
 * `userMessage()` on its platform and may branch on the stable `reason`
 * to say it in its own nouns instead.
 */
final class RecoveryRefused extends RuntimeException
{
    /**
     * @param  string|null  $reason       The stable name of the refusal, or null for one with no connector-side wording.
     * @param  string       $userMessage  The translated sentence for the creator.
     */
    private function __construct(
        public readonly ?string $reason,
        private readonly string $userMessage,
    ) {
        parent::__construct($userMessage);
    }

    /**
     * @param   string|null  $reason       The stable name of the refusal.
     * @param   string       $userMessage  The translated sentence for the creator.
     * @return  self         The exception.
     */
    public static function because(?string $reason, string $userMessage): self
    {
        return new self($reason, $userMessage);
    }

    /**
     * @return  string  The sentence to show the creator, translated.
     */
    public function userMessage(): string
    {
        return $this->userMessage;
    }
}
