<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\InstallationActions;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\Field;
use Subscriby\Connector\Data\InstallationAction;
use Subscriby\Connector\Data\InstallationActionResult;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Enums\FieldType;

/**
 * Two actions a test can run: one with a field and a confirmation, one with neither; anything else is refused.
 */
final class FakeInstallationActions implements InstallationActions
{
    /** The key of the action that asks a question and takes a name. */
    public const string FURNISH = 'furnish_room';

    /** The key of the action that runs at once. */
    public const string TIDY = 'tidy_up';

    /** @var list<array{installation: string, action: string, fields: array<string, mixed>}> Every run, oldest first. */
    public array $runs = [];

    /**
     * @param   InstallationRef           $installation  The installation.
     * @return  list<InstallationAction>  The two actions.
     */
    public function actions(InstallationRef $installation): array
    {
        return [
            new InstallationAction(
                self::FURNISH,
                'Furnish a room',
                'Creates a room with a table and two chairs, and a key for it.',
                [new Field('name', FieldType::Text, 'Room name', required: true, rules: ['string', 'max:60'])],
                'Furnish a new room in this fake hall?',
            ),
            new InstallationAction(self::TIDY, 'Tidy up', 'Puts every chair back where it belongs.'),
        ];
    }

    /**
     * @param   InstallationRef           $installation  The installation.
     * @param   CredentialBag             $credentials   Its secrets.
     * @param   string                    $action        The action key.
     * @param   array<string, mixed>      $fields        The answers.
     * @return  InstallationActionResult  Done for the two known actions, refused for any other key.
     */
    public function run(InstallationRef $installation, CredentialBag $credentials, string $action, array $fields): InstallationActionResult
    {
        if (! in_array($action, [self::FURNISH, self::TIDY], true)) {
            return InstallationActionResult::refused(sprintf('The fake hall has no "%s" action.', $action));
        }

        $this->runs[] = ['installation' => $installation->id, 'action' => $action, 'fields' => $fields];

        return $action === self::FURNISH
            ? InstallationActionResult::done(sprintf('Furnished "%s".', (string) ($fields['name'] ?? 'Room')), ['room' => 'fake-room-'.count($this->runs)])
            : InstallationActionResult::done('Every chair is back in place.');
    }
}
