<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What came of running an installation action: done with a sentence, or refused with a reason.
 *
 * A refusal is an answer, not an exception, because the creator asked for
 * the action and reads the reason on the surface they asked from; an
 * exception is for a platform the connector could not reach at all.
 */
final readonly class InstallationActionResult
{
    /**
     * @param  bool                  $done     True when the action ran.
     * @param  string                $message  What the creator reads: what was done, or why nothing was.
     * @param  array<string, mixed>  $details  Anything a surface may show or an automation may read: ids created, counts.
     * @param  DeliveryFailure|null  $failure  The classified failure when the platform refused, null otherwise.
     */
    public function __construct(
        public bool $done,
        public string $message,
        public array $details = [],
        public ?DeliveryFailure $failure = null,
    ) {}

    /**
     * @param   string                $message  What was done.
     * @param   array<string, mixed>  $details  What a surface may show.
     * @return  self                  A finished action.
     */
    public static function done(string $message, array $details = []): self
    {
        return new self(true, $message, $details);
    }

    /**
     * @param   string                $message  Why nothing was done.
     * @param   DeliveryFailure|null  $failure  The platform's refusal, when it was the platform's.
     * @return  self                  A refused action.
     */
    public static function refused(string $message, ?DeliveryFailure $failure = null): self
    {
        return new self(false, $message, [], $failure);
    }
}
