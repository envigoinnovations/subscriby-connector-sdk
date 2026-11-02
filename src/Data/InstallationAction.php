<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * One thing an installation can do for its creator, declared as data so every surface offers it the same way.
 */
final readonly class InstallationAction
{
    /**
     * @param  string       $key          A lower-case identifier the action is run by.
     * @param  string       $label        What the button says, translated.
     * @param  string       $description  What will happen, in one or two sentences, translated.
     * @param  list<Field>  $fields       What the creator fills in first, empty when nothing is asked.
     * @param  string|null  $confirm      The question a surface asks before running, translated; null when the action needs no confirmation.
     *
     * @throws  InvalidManifest  When the key is not a lower-case identifier or the label is empty.
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $description,
        public array $fields = [],
        public ?string $confirm = null,
    ) {
        if (preg_match('/^[a-z][a-z0-9_]*$/', $key) !== 1) {
            throw InvalidManifest::because('unknown', sprintf('installation action key "%s" must be a lower-case identifier', $key));
        }

        if (trim($label) === '') {
            throw InvalidManifest::because('unknown', sprintf('installation action "%s" needs a label', $key));
        }
    }

    /**
     * @return  bool  True when a surface should ask before running.
     */
    public function needsConfirmation(): bool
    {
        return $this->confirm !== null && trim($this->confirm) !== '';
    }
}
