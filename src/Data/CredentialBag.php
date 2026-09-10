<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The secrets an installation holds, decrypted for the duration of one call.
 *
 * The core stores credentials encrypted and hands a connector this bag when it
 * has to talk to the platform; the connector never sees the column and never
 * decides how the values are kept.
 */
final readonly class CredentialBag
{
    /**
     * @param  array<string, string>  $values  Secrets keyed by the field names the connector declared.
     */
    public function __construct(
        private array $values = [],
    ) {}

    /**
     * @param   string       $key  The field name.
     * @return  string|null  The value, or null when the bag has none under that name.
     */
    public function get(string $key): ?string
    {
        return $this->values[$key] ?? null;
    }

    /**
     * @param   string  $key  The field name.
     * @return  bool    True when a non-empty value is held under that name.
     */
    public function has(string $key): bool
    {
        return ($this->values[$key] ?? '') !== '';
    }

    /**
     * @return  array<string, string>  Every value, for the core to encrypt and store.
     */
    public function toArray(): array
    {
        return $this->values;
    }
}
