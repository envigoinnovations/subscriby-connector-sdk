<?php

declare(strict_types=1);

namespace Subscriby\Connector\Events;

use DateTimeImmutable;
use Subscriby\Connector\Data\InstallationRef;

/**
 * A creator saved an installation's settings, and at least one field changed.
 *
 * Dispatched by the core after its own settings action wrote the row, from
 * whichever surface the creator used (the dashboard, the REST API, the apps),
 * so a connector that declares fields through `SettingsSchema` learns of a
 * choice the moment it is made rather than polling `Core\Installations::settings()`:
 * the forum channel a creator picked becomes the relay space at once, a
 * switch turned off stops the next message. Every setting travels with the
 * event as the core now stores it, because the package that declared the
 * fields is the one reader they were written for.
 */
final readonly class InstallationSettingsChanged
{
    /**
     * @param  InstallationRef       $installation  The installation whose settings changed.
     * @param  list<string>          $changed       The declared names of the fields whose value changed, in the order they were declared.
     * @param  array<string, mixed>  $settings      Every setting as the core now stores it, by declared name.
     * @param  DateTimeImmutable     $changedAt     When the creator saved.
     */
    public function __construct(
        public InstallationRef $installation,
        public array $changed,
        public array $settings,
        public DateTimeImmutable $changedAt,
    ) {}

    /**
     * @param   string  $field  A declared field name.
     * @return  bool    True when that field's value changed in this save.
     */
    public function changed(string $field): bool
    {
        return in_array($field, $this->changed, true);
    }

    /**
     * @param   string  $field    A declared field name.
     * @param   mixed   $default  What to answer when the creator never saved the field.
     * @return  mixed   The value as now stored, or the default.
     */
    public function value(string $field, mixed $default = null): mixed
    {
        return array_key_exists($field, $this->settings) ? $this->settings[$field] : $default;
    }
}
