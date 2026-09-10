<?php

declare(strict_types=1);

namespace Subscriby\Connector\Manifest;

use Illuminate\Contracts\Translation\Translator;
use Subscriby\Connector\Contracts\Ports\SettingsSchema;
use Subscriby\Connector\Data\ConnectorManifest;
use Subscriby\Connector\Data\Field;
use Subscriby\Connector\Data\InstallationRef;

/**
 * The install and settings forms a connector declared in its `connector.json`.
 *
 * The registry binds this for every connector that declares fields and binds
 * no `SettingsSchema` of its own, so a package with a plain form writes no PHP
 * for it; one with fields that depend on the installation binds its own port
 * and declares none in the file. Labels, help texts, steps and option labels
 * in the file are English keys, translated here through the package's own
 * language files so the dashboard, the REST API and the apps show the
 * creator's language.
 */
final class ManifestSettingsSchema implements SettingsSchema
{
    /**
     * @param  ConnectorManifest  $manifest    The manifest the fields were read into.
     * @param  Translator         $translator  Translates the keys the file carries.
     */
    public function __construct(
        private readonly ConnectorManifest $manifest,
        private readonly Translator $translator,
    ) {}

    /**
     * @return  list<Field>  The declared install fields, translated.
     */
    public function installFields(): array
    {
        return array_map($this->translate(...), $this->manifest->installFields);
    }

    /**
     * @param   InstallationRef|null  $installation  Ignored: values are the core's to merge in.
     * @return  list<Field>           The declared settings fields, translated.
     */
    public function settingsFields(?InstallationRef $installation): array
    {
        return array_map($this->translate(...), $this->manifest->settingsFields);
    }

    /**
     * @param   Field  $field  A field as declared.
     * @return  Field  The same field with its label, help, steps and option labels translated; link text is a handle or a name and stays as written.
     */
    private function translate(Field $field): Field
    {
        return new Field(
            name: $field->name,
            type: $field->type,
            label: $this->text($field->label),
            help: $field->help === null ? null : $this->text($field->help),
            required: $field->required,
            rules: $field->rules,
            options: array_map($this->text(...), $field->options),
            value: $field->value,
            steps: array_map($this->text(...), $field->steps),
            links: $field->links,
        );
    }

    /**
     * @param   string  $key  An English key.
     * @return  string  Its translation, or the key itself when none exists.
     */
    private function text(string $key): string
    {
        $translated = $this->translator->get($key);

        return is_string($translated) ? $translated : $key;
    }
}
