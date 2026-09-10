<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What came of sending a message.
 *
 * Platforms answer a refusal with a successful HTTP status and an error body
 * as often as with an exception, so the connector reads its own response and
 * reports one shape here; a caller never has to know which platform it was.
 */
final readonly class DeliveryResult
{
    /**
     * @param  bool                  $delivered          True when the platform accepted the message.
     * @param  string|null           $externalMessageId  The platform's id for the sent message, when it gives one.
     * @param  DeliveryFailure|null  $failure            Why it was not delivered, when it was not.
     */
    public function __construct(
        public bool $delivered,
        public ?string $externalMessageId = null,
        public ?DeliveryFailure $failure = null,
    ) {}

    /**
     * @param   string|null  $externalMessageId  The platform's id for the sent message.
     * @return  self         A delivered message.
     */
    public static function delivered(?string $externalMessageId = null): self
    {
        return new self(true, $externalMessageId);
    }

    /**
     * @param   DeliveryFailure  $failure  Why it was not delivered.
     * @return  self             A failed delivery.
     */
    public static function failed(DeliveryFailure $failure): self
    {
        return new self(false, null, $failure);
    }
}
