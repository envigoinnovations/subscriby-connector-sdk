<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The settings a creator's surface sends to create or change a project, field by field.
 *
 * A field the draft never names keeps its stored value (or the core's
 * default on a create), so a conversation that changes one thing sends one
 * thing; the core validates the shape and refuses what it would refuse on
 * the dashboard. The name is the one field a create needs.
 */
final readonly class ProjectDraft
{
    /** The project's name, 5 to 255 characters. */
    public const string NAME = 'name';

    /** What the project is, up to 1000 characters; null to clear. */
    public const string DESCRIPTION = 'description';

    /** The portal handle, lower-case letters, digits and dashes, unique across every project. */
    public const string HANDLE = 'handle';

    /** The creator's own terms page, or null for none. */
    public const string TERMS_URL = 'terms';

    /** The creator's own privacy page, or null for none. */
    public const string PRIVACY_URL = 'privacy';

    /** Whether the project is live; false archives it, or creates it as a draft. */
    public const string ACTIVE = 'active';

    /** Whether the platform's mark comes off the portal; true needs the tier that allows it. */
    public const string HIDE_BRANDING = 'hide_branding';

    /**
     * @param  array<string, mixed>  $fields  The fields named so far, by the constants above.
     */
    public function __construct(
        public array $fields = [],
    ) {}

    /**
     * @param   string  $field  One of the constants above.
     * @param   mixed   $value  Its value; null clears a field that may be cleared.
     * @return  self    A draft naming this field too.
     */
    public function with(string $field, mixed $value): self
    {
        return new self([...$this->fields, $field => $value]);
    }

    /**
     * @param   string  $field  One of the constants above.
     * @return  bool    Whether the draft names it.
     */
    public function has(string $field): bool
    {
        return array_key_exists($field, $this->fields);
    }
}
