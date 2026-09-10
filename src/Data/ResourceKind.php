<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use InvalidArgumentException;
use Stringable;

/**
 * What kind of thing a resource is: a connector's kind, or the core's own manual perk.
 *
 * Stored and transported as `connector:kind` (`telegram:channel`,
 * `discord:role`), with the bare word `manual` reserved for the perk the
 * creator arranges by hand. A value object rather than an enum because the
 * kinds are declared by connectors the core does not know at compile time.
 */
final readonly class ResourceKind implements Stringable
{
    /** The kind reserved for the core's hand-arranged perk. */
    public const MANUAL = 'manual';

    /**
     * @param  string|null  $connector  The connector key, or null for the manual perk.
     * @param  string       $kind       The kind within that connector, or `manual`.
     */
    private function __construct(
        public ?string $connector,
        public string $kind,
    ) {}

    /**
     * @return  string  The stored form.
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * The manual perk.
     *
     * @return  self  The kind.
     */
    public static function manual(): self
    {
        return new self(null, self::MANUAL);
    }

    /**
     * A connector's kind.
     *
     * @param   string  $connector  The connector key.
     * @param   string  $kind       The kind within it.
     * @return  self    The kind.
     *
     * @throws  InvalidArgumentException  When either part is not a lower-case identifier.
     */
    public static function for(string $connector, string $kind): self
    {
        self::assertIdentifier($connector, 'connector');
        self::assertIdentifier($kind, 'kind');

        return new self($connector, $kind);
    }

    /**
     * Parse the stored form.
     *
     * @param   string  $value  `manual` or `connector:kind`.
     * @return  self    The kind.
     *
     * @throws  InvalidArgumentException  When the value is neither.
     */
    public static function fromString(string $value): self
    {
        if ($value === self::MANUAL) {
            return self::manual();
        }

        $parts = explode(':', $value, 2);

        if (count($parts) !== 2) {
            throw new InvalidArgumentException(sprintf('"%s" is not a resource kind; expected "manual" or "connector:kind".', $value));
        }

        return self::for($parts[0], $parts[1]);
    }

    /**
     * @return  bool  True for the core's manual perk.
     */
    public function isManual(): bool
    {
        return $this->connector === null;
    }

    /**
     * @return  string  The stored form.
     */
    public function toString(): string
    {
        return $this->connector === null ? self::MANUAL : $this->connector.':'.$this->kind;
    }

    /**
     * @param   ResourceKind  $other  The kind to compare with.
     * @return  bool          True when both name the same connector and kind.
     */
    public function equals(self $other): bool
    {
        return $this->connector === $other->connector && $this->kind === $other->kind;
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
            throw new InvalidArgumentException(sprintf('"%s" is not a valid resource %s; use lower-case letters, digits, "_" and "-".', $value, $what));
        }
    }
}
