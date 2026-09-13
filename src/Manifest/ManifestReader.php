<?php

declare(strict_types=1);

namespace Subscriby\Connector\Manifest;

use BackedEnum;
use DateTimeImmutable;
use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * Typed reads over one JSON object of a manifest, collecting every problem rather than stopping at the first.
 *
 * An author fixing a manifest wants the whole list, so a required read records
 * what it found wrong and answers a typed placeholder instead of throwing; the
 * file class calls `throwIfInvalid()` before it builds anything, so no
 * placeholder ever reaches a data object. Nested objects share their parent's
 * list, and the message names the full path (`listing.links.support`).
 */
final class ManifestReader
{
    /** @var list<string> */
    private array $problems;

    /**
     * @param  array<string, mixed>  $data      The object.
     * @param  string                $source    Where it came from, for messages.
     * @param  string                $path      The dotted path of this object within the file, empty at the root.
     * @param  list<string>          $problems  The parent's list to add to; a fresh one at the root.
     */
    public function __construct(
        private readonly array $data,
        private readonly string $source,
        private readonly string $path = '',
        array &$problems = [],
    ) {
        $this->problems = &$problems;
    }

    /**
     * @param   string  $key  The key.
     * @return  bool    Whether the object carries the key at all, whatever its value.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @param   string  $key  The key.
     * @return  string  The non-empty string, or `''` after recording that it is missing or wrong.
     */
    public function string(string $key): string
    {
        if (($this->data[$key] ?? null) === null) {
            return $this->failWith($key, 'is required', '');
        }

        return $this->optionalString($key) ?? '';
    }

    /**
     * @param   string       $key  The key.
     * @return  string|null  The non-empty string, or null when absent; wrong values are recorded and read as absent.
     */
    public function optionalString(string $key): ?string
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            return null;
        }

        if (! is_string($value) || trim($value) === '') {
            return $this->failWith($key, 'must be a non-empty string', null);
        }

        return $value;
    }

    /**
     * @param   string  $key  The key.
     * @return  int     The whole number, or `0` after recording that it is missing or wrong.
     */
    public function int(string $key): int
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            return $this->failWith($key, 'is required', 0);
        }

        if (! is_int($value)) {
            return $this->failWith($key, 'must be a whole number', 0);
        }

        return $value;
    }

    /**
     * @param   string  $key  The key.
     * @return  bool    The flag, or `false` after recording that it is missing or wrong.
     */
    public function bool(string $key): bool
    {
        if (($this->data[$key] ?? null) === null) {
            return $this->failWith($key, 'is required', false);
        }

        return $this->optionalBool($key) ?? false;
    }

    /**
     * @param   string     $key  The key.
     * @return  bool|null  The flag, or null when absent; a non-boolean is recorded and read as absent.
     */
    public function optionalBool(string $key): ?bool
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            return null;
        }

        if (! is_bool($value)) {
            return $this->failWith($key, 'must be true or false', null);
        }

        return $value;
    }

    /**
     * @param   string             $key  The key.
     * @return  DateTimeImmutable  The `YYYY-MM-DD` date, or the epoch after recording that it is missing or wrong.
     */
    public function date(string $key): DateTimeImmutable
    {
        $value = $this->string($key);

        if ($value === '') {
            return new DateTimeImmutable('1970-01-01');
        }

        return ManifestFile::date($value) ?? $this->failWith($key, 'must be a YYYY-MM-DD date', new DateTimeImmutable('1970-01-01'));
    }

    /**
     * @template T of BackedEnum
     *
     * @param   string           $key   The key.
     * @param   class-string<T>  $enum  The enum the value must name a case of.
     * @return  T                The case, or the enum's first case after recording that the value is missing or unknown.
     */
    public function enum(string $key, string $enum): BackedEnum
    {
        $value = $this->string($key);

        if ($value === '') {
            return $enum::cases()[0];
        }

        return $enum::tryFrom($value) ?? $this->failWith($key, sprintf('"%s" is not one of %s', $value, $this->casesOf($enum)), $enum::cases()[0]);
    }

    /**
     * @template T of BackedEnum
     *
     * @param   string           $key       The key.
     * @param   class-string<T>  $enum      The enum every entry must name a case of.
     * @param   bool             $required  Whether an absent list is a problem.
     * @return  list<T>          The cases, unknown entries recorded and left out.
     */
    public function enums(string $key, string $enum, bool $required = true): array
    {
        $cases = [];

        foreach ($this->strings($key, $required) as $index => $value) {
            $case = $enum::tryFrom($value);

            if ($case === null) {
                $this->failWith($key.'['.$index.']', sprintf('"%s" is not one of %s', $value, $this->casesOf($enum)), null);

                continue;
            }

            $cases[] = $case;
        }

        return $cases;
    }

    /**
     * @param   string        $key       The key.
     * @param   bool          $required  Whether an absent list is a problem.
     * @return  list<string>  The strings, non-strings recorded and left out.
     */
    public function strings(string $key, bool $required = true): array
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            return $required ? $this->failWith($key, 'is required', []) : [];
        }

        if (! is_array($value) || ! array_is_list($value)) {
            return $this->failWith($key, 'must be a list', []);
        }

        $strings = [];

        foreach ($value as $index => $entry) {
            if (! is_string($entry) || trim($entry) === '') {
                $this->failWith($key.'['.$index.']', 'must be a non-empty string', null);

                continue;
            }

            $strings[] = $entry;
        }

        return $strings;
    }

    /**
     * @param   string                 $key  The key.
     * @return  array<string, string>  String to string, wrong entries recorded and left out; empty when absent.
     */
    public function stringMap(string $key): array
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            return [];
        }

        if (! is_array($value) || ($value !== [] && array_is_list($value))) {
            return $this->failWith($key, 'must be an object of strings', []);
        }

        $map = [];

        foreach ($value as $entry => $label) {
            if (! is_string($label) || trim($label) === '') {
                $this->failWith($key.'.'.$entry, 'must be a non-empty string', null);

                continue;
            }

            $map[(string) $entry] = $label;
        }

        return $map;
    }

    /**
     * @param   string  $key       The key.
     * @param   bool    $required  Whether an absent object is a problem.
     * @return  self    A reader over the nested object; over nothing when absent or wrong, so reads on it record their own absence.
     */
    public function object(string $key, bool $required = true): self
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            if ($required) {
                $this->failWith($key, 'is required', null);
            }

            return new self([], $this->source, $this->at($key), $this->problems);
        }

        if (! is_array($value) || ($value !== [] && array_is_list($value))) {
            $this->failWith($key, 'must be an object', null);

            return new self([], $this->source, $this->at($key), $this->problems);
        }

        return new self($value, $this->source, $this->at($key), $this->problems);
    }

    /**
     * @param   string      $key       The key.
     * @param   bool        $required  Whether an absent list is a problem.
     * @return  list<self>  A reader per object in the list, non-objects recorded and left out.
     */
    public function objects(string $key, bool $required = true): array
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            return $required ? $this->failWith($key, 'is required', []) : [];
        }

        if (! is_array($value) || ! array_is_list($value)) {
            return $this->failWith($key, 'must be a list', []);
        }

        $readers = [];

        foreach ($value as $index => $entry) {
            if (! is_array($entry) || ($entry !== [] && array_is_list($entry))) {
                $this->failWith($key.'['.$index.']', 'must be an object', null);

                continue;
            }

            $readers[] = new self($entry, $this->source, $this->at($key.'['.$index.']'), $this->problems);
        }

        return $readers;
    }

    /**
     * Record every key this object carries that the schema does not know, so a typo cannot pass as an absent optional.
     *
     * @param  list<string>  $known  The keys the schema allows here.
     */
    public function refuseUnknownKeys(array $known): void
    {
        foreach (array_keys($this->data) as $key) {
            if (! in_array((string) $key, $known, true)) {
                $this->failWith((string) $key, 'is not a manifest key', null);
            }
        }
    }

    /**
     * Record a problem about this object as a whole.
     *
     * @param  string  $problem  What is wrong.
     */
    public function fail(string $problem): void
    {
        $this->problems[] = $this->path === '' ? $problem : $this->path.': '.$problem;
    }

    /**
     * @return  int  How many problems have been recorded so far, so a caller can tell whether its own reads added any.
     */
    public function problemCount(): int
    {
        return count($this->problems);
    }

    /**
     * @param  string  $key  The connector key, for the exception.
     *
     * @throws  InvalidManifest  Listing every problem recorded, when there is one.
     */
    public function throwIfInvalid(string $key): void
    {
        if ($this->problems === []) {
            return;
        }

        throw InvalidManifest::because($key, sprintf('%s: %s', $this->source, implode('; ', $this->problems)));
    }

    /**
     * Record a problem about one key and answer the placeholder the caller hands back.
     *
     * @template TPlaceholder
     *
     * @param   string        $key          The key, or a bracketed path below it.
     * @param   string        $problem      What is wrong with it.
     * @param   TPlaceholder  $placeholder  What the caller returns in place of a value.
     * @return  TPlaceholder  The placeholder.
     */
    private function failWith(string $key, string $problem, mixed $placeholder): mixed
    {
        $this->problems[] = $this->at($key).' '.$problem;

        return $placeholder;
    }

    /**
     * @param   class-string<BackedEnum>  $enum  An enum.
     * @return  string                    Its case values, comma separated.
     */
    private function casesOf(string $enum): string
    {
        return implode(', ', array_map(fn (BackedEnum $case): string => (string) $case->value, $enum::cases()));
    }

    /**
     * @param   string  $key  A key in this object.
     * @return  string  Its dotted path from the file root.
     */
    private function at(string $key): string
    {
        return $this->path === '' ? $key : $this->path.'.'.$key;
    }
}
