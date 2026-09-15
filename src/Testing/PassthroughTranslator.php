<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing;

use Countable;
use Illuminate\Contracts\Translation\Translator;

/**
 * A translator that hands every key back as written, for tests that run without an application.
 *
 * The kit's `TestRegistry` binds the manifest-backed settings form through
 * it, so a package's own suite reads the labels its `connector.json` declares
 * without Laravel's translator and a loaded language line behind it. It does
 * what the real translator does with a key it has no line for: returns the key
 * with its `:placeholder` replacements applied, and for a `singular|plural`
 * key picks the singular for exactly one and the plural otherwise.
 */
final class PassthroughTranslator implements Translator
{
    private string $locale = 'en';

    /**
     * @param   string                $key      The key as written.
     * @param   array<string, mixed>  $replace  Placeholders to substitute, named without their leading colon.
     * @param   string|null           $locale   Ignored: there is one locale and it is the key's.
     * @return  string                The key with its placeholders filled in.
     */
    public function get($key, array $replace = [], $locale = null): string
    {
        $text = (string) $key;

        foreach ($replace as $placeholder => $value) {
            $text = str_replace(':'.$placeholder, (string) $value, $text);
        }

        return $text;
    }

    /**
     * @param   string                     $key      A `singular|plural` key, or a plain one.
     * @param   Countable|int|float|array  $number   What is being counted.
     * @param   array<string, mixed>       $replace  Placeholders to substitute.
     * @param   string|null                $locale   Ignored.
     * @return  string                     The singular form for exactly one, the plural otherwise, placeholders filled in.
     */
    public function choice($key, $number, array $replace = [], $locale = null): string
    {
        $count = is_countable($number) ? count($number) : $number;
        $forms = explode('|', (string) $key);
        $form = (float) $count === 1.0 ? $forms[0] : ($forms[1] ?? $forms[0]);

        return $this->get($form, $replace);
    }

    /**
     * @return  string  The locale the last `setLocale()` named, `en` until then.
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param  string  $locale  Remembered for `getLocale()`; it changes no translation.
     */
    public function setLocale($locale): void
    {
        $this->locale = (string) $locale;
    }
}
