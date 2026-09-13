<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use InvalidArgumentException;
use Stringable;

/**
 * Which provider a payment method charges through: a gateway's own word, or a connector's native provider.
 *
 * Stored and transported as the gateway's slug (`stripe`, `paypal`) or as
 * `connector:provider` for a currency that exists only because of a
 * connector. A value object rather than an enum because native
 * providers are declared by connectors the core does not know at compile
 * time, and a project carries one method per provider key, so two connectors
 * may each bring their own currency to the same project.
 */
final readonly class PaymentProviderKey implements Stringable
{
    /** The shape of a key: a lower-case word, or two joined by a colon. */
    private const string PATTERN = '/^[a-z][a-z0-9_-]*(:[a-z][a-z0-9_-]*)?$/';

    /**
     * @param  string|null  $connector  The connector key, or null for a gateway the core charges through itself.
     * @param  string       $provider   The provider's word within that connector, or the gateway's slug.
     */
    private function __construct(
        public ?string $connector,
        public string $provider,
    ) {}

    /**
     * @return  string  The stored form.
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * A provider a connector brings.
     *
     * @param   string  $connector  The connector key.
     * @param   string  $provider   The provider's word within that connector.
     * @return  self    The key.
     *
     * @throws  InvalidArgumentException  When either part is not a lower-case identifier.
     */
    public static function native(string $connector, string $provider): self
    {
        self::assertIdentifier($connector, 'connector');
        self::assertIdentifier($provider, 'provider');

        return new self($connector, $provider);
    }

    /**
     * A gateway the core charges through itself.
     *
     * @param   string  $provider  The gateway's slug.
     * @return  self    The key.
     *
     * @throws  InvalidArgumentException  When the slug is not a lower-case identifier.
     */
    public static function gateway(string $provider): self
    {
        self::assertIdentifier($provider, 'provider');

        return new self(null, $provider);
    }

    /**
     * Parse the stored form.
     *
     * @param   string  $value  `provider` or `connector:provider`.
     * @return  self    The key.
     *
     * @throws  InvalidArgumentException  When the value is neither.
     */
    public static function fromString(string $value): self
    {
        $parts = explode(':', $value);

        return match (count($parts)) {
            1 => self::gateway($parts[0]),
            2 => self::native($parts[0], $parts[1]),
            default => throw new InvalidArgumentException(sprintf('"%s" is not a payment provider key; expected "provider" or "connector:provider".', $value)),
        };
    }

    /**
     * Parse the stored form when it may be missing or malformed.
     *
     * @param   string|null  $value  The candidate.
     * @return  self|null    The key, or null when the value is empty or not a key.
     */
    public static function tryFromString(?string $value): ?self
    {
        if ($value === null || preg_match(self::PATTERN, $value) !== 1) {
            return null;
        }

        return self::fromString($value);
    }

    /**
     * @return  bool  True when a connector brings the provider.
     */
    public function isNative(): bool
    {
        return $this->connector !== null;
    }

    /**
     * @return  string  The stored form.
     */
    public function toString(): string
    {
        return $this->connector === null ? $this->provider : $this->connector.':'.$this->provider;
    }

    /**
     * @param   PaymentProviderKey|string  $other  The key to compare with, parsed when given as its stored form.
     * @return  bool                       True when both name the same connector and provider.
     */
    public function equals(self|string $other): bool
    {
        $other = is_string($other) ? self::tryFromString($other) : $other;

        return $other !== null && $this->connector === $other->connector && $this->provider === $other->provider;
    }

    /**
     * @param  string  $value  The candidate.
     * @param  string  $what   Which part it is, for the message.
     *
     * @throws  InvalidArgumentException  When the value is not a lower-case identifier.
     */
    private static function assertIdentifier(string $value, string $what): void
    {
        if (preg_match('/^[a-z][a-z0-9_-]*$/', $value) !== 1) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid payment provider %s; use lower-case letters, digits, "_" and "-".', $value, $what));
        }
    }
}
