<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\FieldType;
use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * One field of a connector's install or settings form, declared as data.
 *
 * The dashboard, the REST API and the mobile apps all render the same fields,
 * so a connector needs no Blade to be installable and the same validation
 * rules run wherever the form is filled in. A walkthrough is data too: the
 * numbered steps a creator follows on the platform before pasting a credential
 * travel as `steps`, and `links` names the text every renderer turns into a
 * link, so the apps show the same instructions the dashboard does.
 */
final readonly class Field
{
    /** The placeholder renderers replace with the product name. */
    public const string APP_PLACEHOLDER = ':app';

    /** The placeholder renderers replace with the label of the button that submits the form. */
    public const string BUTTON_PLACEHOLDER = ':button';

    /**
     * @param  string                 $name      The key the value is stored and submitted under, a lower-case identifier.
     * @param  FieldType              $type      What the field is.
     * @param  string                 $label     What the creator reads above it.
     * @param  string|null            $help      A sentence under it, when one is needed.
     * @param  bool                   $required  Whether a value must be given.
     * @param  list<string>           $rules     Laravel validation rules the value must pass.
     * @param  array<string, string>  $options   For a select: value to label.
     * @param  string|null            $value     The current value, for a settings form.
     * @param  list<string>           $steps     Numbered steps shown with the field, in order; may carry `:app` and `:button`.
     * @param  array<string, string>  $links     Text to turn into a link wherever it appears in the label, help or steps: text to `https://` or `mailto:` target.
     *
     * @throws  InvalidManifest  When the name is not an identifier, a select has no options, or a link target is not https or mailto.
     */
    public function __construct(
        public string $name,
        public FieldType $type,
        public string $label,
        public ?string $help = null,
        public bool $required = false,
        public array $rules = [],
        public array $options = [],
        public ?string $value = null,
        public array $steps = [],
        public array $links = [],
    ) {
        if (preg_match('/^[a-z][a-z0-9_]*$/', $name) !== 1) {
            throw InvalidManifest::because('unknown', sprintf('field name "%s" must be a lower-case identifier', $name));
        }

        if ($type === FieldType::Select && $options === []) {
            throw InvalidManifest::because('unknown', sprintf('select field "%s" needs options', $name));
        }

        foreach ($links as $text => $target) {
            if (preg_match('#^(https://[^\s/]+|mailto:[^\s@]+@[^\s]+)#', $target) !== 1) {
                throw InvalidManifest::because('unknown', sprintf('field "%s" links "%s" to "%s", which is not an https URL or a mailto address', $name, $text, $target));
            }
        }
    }
}
