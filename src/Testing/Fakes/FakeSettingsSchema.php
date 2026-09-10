<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\SettingsSchema;
use Subscriby\Connector\Data\Field;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Enums\FieldType;

/**
 * One secret to install with and one text setting to edit afterwards.
 */
final class FakeSettingsSchema implements SettingsSchema
{
    /**
     * @return  list<Field>  A single required token.
     */
    public function installFields(): array
    {
        return [
            new Field('token', FieldType::Secret, 'Fake token', 'Paste the token the fake platform gave you.', true, ['string', 'min:4']),
        ];
    }

    /**
     * @param   InstallationRef|null  $installation  The installation being edited.
     * @return  list<Field>           A greeting the fake bot sends first.
     */
    public function settingsFields(?InstallationRef $installation): array
    {
        return [
            new Field('greeting', FieldType::Text, 'Greeting', 'What the fake bot says first.', false, ['string', 'max:120']),
        ];
    }
}
