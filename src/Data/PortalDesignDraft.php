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
     * @param  string|null                       $preset                A preset's value, which recolours every colour from it, or null to keep the colours.
     * @param  string|null                       $colorMode             A colour mode's value, or null to keep it.
     * @param  string|null                       $accent                The accent as a hex code, or null to keep it.
     * @param  string|null                       $accentSecondary       The second accent as a hex code, `NONE` for the preset's own, or null to keep it.
     * @param  string|null                       $background            The page colour as a hex code, or null to keep it.
     * @param  bool|null                         $grid                  Whether the grid shows, or null to keep it.
     * @param  string|null                       $surface               The card colour as a hex code, or null to keep it.
     * @param  string|null                       $text                  The body text colour as a hex code, or null to keep it.
     * @param  string|null                       $heading               The heading colour as a hex code, or null to keep it.
     * @param  string|null                       $muted                 The caption colour as a hex code, or null to keep it.
     * @param  string|null                       $tagline               The tagline, `NONE` for no tagline, or null to keep it.
     * @param  list<PortalSocialLinkDraft>|null  $socialLinks           The whole social-link row in order, an empty list for no row, or null to keep it.
     * @param  string|null                       $fontDisplay           A face's value for the headings, or null to keep it.
     * @param  string|null                       $fontBody              A face's value for the body, or null to keep it.
     * @param  string|null                       $headingCase           A heading case's value, or null to keep it.
     * @param  string|null                       $typeScale             A type scale's value, or null to keep it.
     * @param  string|null                       $radius                A corner setting's value, or null to keep it.
     * @param  string|null                       $buttonStyle           A button shape's value, or null to keep it.
     * @param  string|null                       $buttonFill            A button fill's value, or null to keep it.
     * @param  string|null                       $buttonCase            A text case's value for the button labels, or null to keep it.
     * @param  string|null                       $shadow                A shadow depth's value, or null to keep it.
     * @param  bool|null                         $cardBorder            Whether the surface cards draw a border, or null to keep it.
     * @param  string|null                       $arrangement           An arrangement's value, or null to keep it.
     * @param  string|null                       $contentWidth          A content width's value, or null to keep it.
     * @param  bool|null                         $stickySidebar         Whether the sidebar card stays in view, or null to keep it.
     * @param  string|null                       $planGrid              A plan-column setting's value, or null to keep it.
     * @param  string|null                       $planDensity           A plan density's value, or null to keep it.
     * @param  bool|null                         $plansHeading          Whether the plan list opens with its heading, or null to keep it.
     * @param  bool|null                         $metricsSubscribers    Whether the subscriber count shows, or null to keep it.
     * @param  bool|null                         $metricsFollowers      Whether the follower count shows, or null to keep it.
     * @param  int|null                          $metricsThreshold      The count a figure must reach before it shows, or null to keep it.
     * @param  string|null                       $plansTitle            The heading over the plan list, `NONE` for the built-in wording, or null to keep it.
     * @param  string|null                       $plansSubtitle         The line under the plan list heading, `NONE` for the built-in wording, or null to keep it.
     * @param  string|null                       $labelLogin            The header's sign-in button, `NONE` for the built-in wording, or null to keep it.
     * @param  string|null                       $labelShare            The header's share button, `NONE` for the built-in wording, or null to keep it.
     * @param  string|null                       $labelSubscribe        The button on a plan that sells, `NONE` for the built-in wording, or null to keep it.
     * @param  string|null                       $checkoutTitle         The checkout heading, `NONE` for the built-in wording, or null to keep it.
     * @param  string|null                       $checkoutSubtitle      The line under the checkout heading, `NONE` for the built-in wording, or null to keep it.
     * @param  string|null                       $checkoutNote          The creator's sentence above the acceptance line, `NONE` for the built-in wording, or null to keep it.
     * @param  string|null                       $successTitle          The heading of the success screen, `NONE` for the built-in wording, or null to keep it.
     * @param  string|null                       $successMessage        The creator's thank-you under the confirmation, `NONE` for the built-in wording, or null to keep it.
     * @param  string|null                       $perksTitle            The heading over the perks, `NONE` for the built-in wording, or null to keep it.
     * @param  list<string>|null                 $perks                 The whole perk list in order, or null to keep it.
     * @param  string|null                       $faqTitle              The heading over the questions, `NONE` for the built-in wording, or null to keep it.
     * @param  list<PortalFaqItemDraft>|null     $faq                   The whole question list in order, or null to keep it.
     * @param  string|null                       $aboutName             The creator's name, `NONE` for none, or null to keep it.
     * @param  string|null                       $aboutBio              A few lines about the creator, `NONE` for none, or null to keep it.
     * @param  string|null                       $customButtonLabel     The second sidebar button's label, `NONE` for none, or null to keep it.
     * @param  string|null                       $customButtonUrl       The second sidebar button's address, `NONE` for none, or null to keep it.
     * @param  string|null                       $footerRefundUrl       The footer's refund policy link, `NONE` for none, or null to keep it.
     * @param  string|null                       $footerContactUrl      The footer's contact link, a page or a mailto: address, `NONE` for none, or null to keep it.
     * @param  string|null                       $footerImprintUrl      The footer's imprint link, `NONE` for none, or null to keep it.
     * @param  string|null                       $footerLine            A line of the creator's under the footer links, `NONE` for none, or null to keep it.
     * @param  bool|null                         $footerHelp            Whether the footer carries the Help link, or null to keep it.
     * @param  string|null                       $announcementText      The strip's text, `NONE` for no strip, or null to keep it.
     * @param  string|null                       $announcementUrl       Where the strip leads, `NONE` for nowhere, or null to keep it.
     * @param  string|null                       $announcementStartsAt  When it starts, ISO 8601 with a zone, `NONE` for at once, or null to keep it.
     * @param  string|null                       $announcementEndsAt    When it stops, ISO 8601 with a zone, `NONE` for never, or null to keep it.
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
        public ?string $arrangement = null,
        public ?string $contentWidth = null,
        public ?bool $stickySidebar = null,
        public ?string $planGrid = null,
        public ?string $planDensity = null,
        public ?bool $plansHeading = null,
        public ?bool $metricsSubscribers = null,
        public ?bool $metricsFollowers = null,
        public ?int $metricsThreshold = null,
        public ?string $plansTitle = null,
        public ?string $plansSubtitle = null,
        public ?string $labelLogin = null,
        public ?string $labelShare = null,
        public ?string $labelSubscribe = null,
        public ?string $checkoutTitle = null,
        public ?string $checkoutSubtitle = null,
        public ?string $checkoutNote = null,
        public ?string $successTitle = null,
        public ?string $successMessage = null,
        public ?string $perksTitle = null,
        public ?array $perks = null,
        public ?string $faqTitle = null,
        public ?array $faq = null,
        public ?string $aboutName = null,
        public ?string $aboutBio = null,
        public ?string $customButtonLabel = null,
        public ?string $customButtonUrl = null,
        public ?string $footerRefundUrl = null,
        public ?string $footerContactUrl = null,
        public ?string $footerImprintUrl = null,
        public ?string $footerLine = null,
        public ?bool $footerHelp = null,
        public ?string $announcementText = null,
        public ?string $announcementUrl = null,
        public ?string $announcementStartsAt = null,
        public ?string $announcementEndsAt = null,
    ) {}
}
