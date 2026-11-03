<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A project as its creator manages it: the settings a creator surface shows and edits.
 *
 * The portal address is the core's, built from the handle; the connector of
 * the live installation says where the project sells; the branding switch
 * and the legal links are the settings a creator may change from a
 * conversation, with the same tier rules the dashboard applies.
 */
final readonly class ProjectDetails
{
    /**
     * @param  string       $id              The project's UUID.
     * @param  string       $name            The project's name.
     * @param  string|null  $description     What the project is, in the creator's words, or null.
     * @param  string|null  $handle          The portal handle, null while the project has none.
     * @param  string|null  $portalUrl       The portal's address, null while the project has no handle.
     * @param  bool         $active          Whether the project is live rather than archived or a draft.
     * @param  bool         $hideBranding    Whether the platform's mark is off the portal.
     * @param  string|null  $termsUrl        The creator's own terms page, or null.
     * @param  string|null  $privacyUrl      The creator's own privacy page, or null.
     * @param  string|null  $connector       The connector of its live installation, null while nothing is connected.
     * @param  bool         $supportEnabled  Whether the project takes support messages.
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public ?string $handle,
        public ?string $portalUrl,
        public bool $active,
        public bool $hideBranding,
        public ?string $termsUrl,
        public ?string $privacyUrl,
        public ?string $connector,
        public bool $supportEnabled,
    ) {}

    /**
     * @return  ProjectRef  The ref every other contract takes.
     */
    public function ref(): ProjectRef
    {
        return new ProjectRef($this->id);
    }
}
