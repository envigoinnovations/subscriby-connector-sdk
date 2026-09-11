<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\DeliveryFailureKind;

/**
 * Why a platform call did not do what was asked, classified.
 */
final readonly class DeliveryFailure
{
    /** The `code` prefix a connector uses to say a place keeps the account out and the connector cannot lift it; the standing follows the colon. */
    public const string MEMBER_HELD_OUT = 'member_held_out';

    /**
     * @param  DeliveryFailureKind  $kind               The class of failure the core acts on.
     * @param  string               $detail             The platform's own words, for the log and the creator.
     * @param  int|null             $retryAfterSeconds  How long the platform asked us to wait, for a rate limit.
     * @param  string|null          $code               The connector's own word for the refusal (`user_deactivated`), stored where the core keeps a reason; null when it has none.
     */
    public function __construct(
        public DeliveryFailureKind $kind,
        public string $detail = '',
        public ?int $retryAfterSeconds = null,
        public ?string $code = null,
    ) {}

    /**
     * A grant refused because the place itself keeps the account out and the connector could not lift it.
     *
     * The one refusal the core tells both sides about: the member is asked to
     * contact the organiser and the creator to clear their banned list by hand,
     * so it is named here rather than read out of a sentence.
     *
     * @param   string  $standing  The platform's word for the standing that keeps the account out (`kicked`, `restricted`).
     * @return  self    A `NotPermitted` failure carrying the standing in its code.
     */
    public static function memberHeldOut(string $standing): self
    {
        return new self(
            DeliveryFailureKind::NotPermitted,
            sprintf('The member is still %s in the place after the connector tried to lift it; the connector lacks the right to.', $standing),
            null,
            self::MEMBER_HELD_OUT.':'.$standing,
        );
    }

    /**
     * @return  string|null  The standing that keeps the account out, when this failure is the place refusing them; null for any other failure.
     */
    public function heldOutStanding(): ?string
    {
        if ($this->code === null || ! str_starts_with($this->code, self::MEMBER_HELD_OUT.':')) {
            return null;
        }

        $standing = substr($this->code, strlen(self::MEMBER_HELD_OUT) + 1);

        return $standing === '' ? null : $standing;
    }
}
