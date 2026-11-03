<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One value a gateway needs before it can take money: a key, a secret, an account email.
 *
 * Every field is typed by the creator and stored encrypted by the core; a
 * connector shows the label, collects the value and never keeps it.
 */
final readonly class PaymentFieldOption
{
    /**
     * @param  string                 $name     The field, as the draft names it.
     * @param  string                 $label    What the creator reads, in their language.
     * @param  array<string, string>  $choices  Value to label when the field is a pick rather than free text; empty for free text.
     */
    public function __construct(
        public string $name,
        public string $label,
        public array $choices = [],
    ) {}
}
