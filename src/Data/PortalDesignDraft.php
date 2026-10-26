<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a creator changed on the portal design, one choice or several.
 *
 * Null means "leave it as it is", so a bot that answers one question sends
 * one field; the tagline and the second accent are cleared by sending the
 * empty string, since null cannot mean both. The social-link row is always
 * sent whole, because a row is edited by adding and removing links.
 */
final readonly class PortalDesignDraft
{
    /** The value that clears the tagline or the second accent. */
    public const string NONE = '';

    /**
     * @param  string|null                       $preset           A preset's value, which recolours every colour from it, or null to keep the colours.
     * @param  string|null                       $colorMode        A colour mode's value, or null to keep it.
     * @param  string|null                       $accent           The accent as a hex code, or null to keep it.
     * @param  string|null                       $accentSecondary  The second accent as a hex code, `NONE` for the preset's own, or null to keep it.
     * @param  string|null                       $background       The page colour as a hex code, or null to keep it.
     * @param  bool|null                         $grid             Whether the grid shows, or null to keep it.
     * @param  string|null                       $surface          The card colour as a hex code, or null to keep it.
     * @param  string|null                       $text             The body text colour as a hex code, or null to keep it.
     * @param  string|null                       $heading          The heading colour as a hex code, or null to keep it.
     * @param  string|null                       $muted            The caption colour as a hex code, or null to keep it.
     * @param  string|null                       $tagline          The tagline, `NONE` for no tagline, or null to keep it.
     * @param  list<PortalSocialLinkDraft>|null  $socialLinks      The whole social-link row in order, an empty list for no row, or null to keep it.
     * @param  string|null                       $fontDisplay      A face's value for the headings, or null to keep it.
     * @param  string|null                       $fontBody         A face's value for the body, or null to keep it.
     * @param  string|null                       $headingCase      A heading case's value, or null to keep it.
     * @param  string|null                       $typeScale        A type scale's value, or null to keep it.
     * @param  string|null                       $radius           A corner setting's value, or null to keep it.
     * @param  string|null                       $buttonStyle      A button shape's value, or null to keep it.
     * @param  string|null                       $buttonFill       A button fill's value, or null to keep it.
     * @param  string|null                       $buttonCase       A text case's value for the button labels, or null to keep it.
     * @param  string|null                       $shadow           A shadow depth's value, or null to keep it.
     * @param  bool|null                         $cardBorder       Whether the surface cards draw a border, or null to keep it.
     */
    public function __construct(
        public ?string $preset = null,
        public ?string $colorMode = null,
        public ?string $accent = null,
        public ?string $accentSecondary = null,
        public ?string $background = null,
        public ?bool $grid = null,
        public ?string $surface = null,
        public ?string $text = null,
        public ?string $heading = null,
        public ?string $muted = null,
        public ?string $tagline = null,
        public ?array $socialLinks = null,
        public ?string $fontDisplay = null,
        public ?string $fontBody = null,
        public ?string $headingCase = null,
        public ?string $typeScale = null,
        public ?string $radius = null,
        public ?string $buttonStyle = null,
        public ?string $buttonFill = null,
        public ?string $buttonCase = null,
        public ?string $shadow = null,
        public ?bool $cardBorder = null,
    ) {}
}
