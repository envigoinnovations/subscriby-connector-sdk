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

    /**
     * A bag holding these values and more.
     *
     * A connector cannot write the core's credential column, so what it mints
     * while connecting (a webhook signing secret) rides along in
     * {@see InstallationSummary::$meta} and is merged here, the given keys
     * winning over the ones already held. Scalars are kept as strings and
     * anything else as JSON, because the column holds strings.
     *
     * @param   array<string, mixed>  $values  What to add.
     * @return  self                  A new bag; this one is unchanged.
     */
    public function with(array $values): self
    {
        $merged = $this->values;

        foreach ($values as $key => $value) {
            $merged[(string) $key] = is_scalar($value) || $value === null ? (string) $value : (string) json_encode($value);
        }

        return new self($merged);
    }
}
