<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\PortalDesignDetails;
use Subscriby\Connector\Data\PortalDesignDraft;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Exceptions\PortalDesignRefused;

/**
 * A project's portal design as a connector's creator surface edits it.
 *
 * The portal page every project gets is shaped by one design document,
 * published beside a draft: the creator changes the draft, previews it on
 * the real page through a signed address, and publishes when it is right.
 * A connector offers the same choices in its own idiom (a bot's buttons and
 * typed answers) and never composes a document of its own: every call takes
 * the `CreatorRef` of the account that is talking and the `ProjectRef` of
 * the project, runs as that creator under the project's permissions exactly
 * as the dashboard's designer does, and answers the whole design with every
 * option and label the screens print, in the creator's language. The images
 * (the banner and the project photo) are not part of the design document
 * and stay on the dashboard until a connector can take an upload.
 */
interface PortalDesign
{
    /**
     * The design a creator edits: the draft when one waits, else the published design.
     *
     * @param   CreatorRef           $creator  The creator asking.
     * @param   ProjectRef           $project  The project.
     * @return  PortalDesignDetails  Every choice with its label, the options each control offers, and the preview address.
     *
     * @throws  PortalDesignRefused  `project_unknown` when the creator may not see the project.
     */
    public function designOf(CreatorRef $creator, ProjectRef $project): PortalDesignDetails;

    /**
     * Change some choices of the draft, starting one from the published design when none waits.
     *
     * The core rebuilds and validates the whole document on every save, so a
     * draft names only what changed and nothing a visitor sees moves until
     * `publish()`.
     *
     * @param   CreatorRef           $creator  The creator saving.
     * @param   ProjectRef           $project  The project.
     * @param   PortalDesignDraft    $draft    The choices that changed; null keeps a choice as it is.
     * @return  PortalDesignDetails  The draft after the change.
     *
     * @throws  PortalDesignRefused  `project_unknown`, `forbidden` when the creator may not update the project, `invalid_design` with the core's sentence when a value is not one the portal can draw.
     */
    public function saveDraft(CreatorRef $creator, ProjectRef $project, PortalDesignDraft $draft): PortalDesignDetails;

    /**
     * Make the draft the design every visitor sees.
     *
     * @param   CreatorRef           $creator  The creator publishing.
     * @param   ProjectRef           $project  The project.
     * @return  PortalDesignDetails  The design now published.
     *
     * @throws  PortalDesignRefused  `project_unknown`, `forbidden`, `no_draft` when nothing waits to be published.
     */
    public function publish(CreatorRef $creator, ProjectRef $project): PortalDesignDetails;

    /**
     * Throw the draft away and keep what is published.
     *
     * @param   CreatorRef           $creator  The creator discarding.
     * @param   ProjectRef           $project  The project.
     * @return  PortalDesignDetails  The published design, the one the screens show again.
     *
     * @throws  PortalDesignRefused  `project_unknown`, `forbidden`, `no_draft` when nothing is drafted.
     */
    public function discardDraft(CreatorRef $creator, ProjectRef $project): PortalDesignDetails;

    /**
     * Put one section of the draft back to the defaults, leaving the other sections as drafted.
     *
     * @param   CreatorRef           $creator  The creator resetting.
     * @param   ProjectRef           $project  The project.
     * @param   string               $section  A section's value from `PortalDesignDetails::$sections`.
     * @return  PortalDesignDetails  The draft after the reset.
     *
     * @throws  PortalDesignRefused  `project_unknown`, `forbidden`, `section_unknown` for a value the catalogue does not carry.
     */
    public function resetSection(CreatorRef $creator, ProjectRef $project, string $section): PortalDesignDetails;
}
