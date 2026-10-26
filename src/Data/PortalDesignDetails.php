<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * A project's portal design as a creator's connector surface shows and edits it.
 *
 * Every choice comes twice over: the stored value a save sends back and the
 * label the core phrases for it, so a screen prints words and never maps a
 * value to a name of its own; every control's options ride along the same
 * way. The colours are six-digit lowercase hex codes, the one value a
 * creator types as well as reads.
 */
final readonly class PortalDesignDetails
{
    /**
     * @param  string                         $projectId             The project.
     * @param  string                         $projectName           Its name, for the screen's heading.
     * @param  bool                           $hasDraft              Whether a draft waits to be published or discarded.
     * @param  DateTimeImmutable|null         $draftSavedAt          When the draft was last saved, or null without one.
     * @param  string|null                    $previewUrl            A signed address that shows the draft on the real portal page for ten minutes, or null while the project has no portal page.
     * @param  string|null                    $portalUrl             The portal page itself, or null while the project has no handle.
     * @param  PortalDesignOption             $preset                The palette the colours started from.
     * @param  list<PortalDesignOption>       $presets               Every preset a creator may pick.
     * @param  PortalDesignOption             $colorMode             Which half of the preset the portal paints with.
     * @param  list<PortalDesignOption>       $colorModes            Every colour mode a creator may pick.
     * @param  string                         $accent                The accent.
     * @param  string|null                    $accentSecondary       The second accent the creator chose, or null for the preset's own.
     * @param  string                         $accentSecondaryInUse  The second accent the portal paints with: the creator's, else the preset's own.
     * @param  string                         $background            The page colour.
     * @param  bool                           $grid                  Whether the faint grid shows over the page colour.
     * @param  string                         $surface               The card colour.
     * @param  string                         $text                  The body text colour.
     * @param  string                         $heading               The heading colour.
     * @param  string                         $muted                 The colour of captions and labels.
     * @param  string|null                    $tagline               One line under the project's name, or null for none.
     * @param  int                            $taglineMax            The most characters a tagline may hold.
     * @param  list<PortalSocialLinkDetails>  $socialLinks           The social-link row in order; empty hides the row.
     * @param  int                            $socialLinksMax        The most links the row may hold.
     * @param  list<PortalDesignOption>       $networks              Every network a link may point at, the custom one included.
     * @param  string                         $customNetwork         The value of the one network whose link the creator names.
     * @param  list<PortalDesignOption>       $sections              The sections a reset may name.
     * @param  PortalDesignOption             $fontDisplay           The face the headings and figures are set in.
     * @param  PortalDesignOption             $fontBody              The face the rest of the text is set in.
     * @param  list<PortalDesignOption>       $fonts                 Every face either may be, the two the portal always had first.
     * @param  PortalDesignOption             $headingCase           Whether the headings are set in capitals or as written.
     * @param  list<PortalDesignOption>       $headingCases          The two cases.
     * @param  PortalDesignOption             $typeScale             How large the type is.
     * @param  list<PortalDesignOption>       $typeScales            The three scales.
     * @param  PortalDesignOption             $radius                How rounded every corner is.
     * @param  list<PortalDesignOption>       $radii                 The three corner settings.
     * @param  PortalDesignOption             $buttonStyle           The shape of the buttons.
     * @param  list<PortalDesignOption>       $buttonStyles          The three button shapes.
     * @param  PortalDesignOption             $buttonFill            How the buttons are filled.
     * @param  list<PortalDesignOption>       $buttonFills           The three fills.
     * @param  PortalDesignOption             $buttonCase            Whether button labels are set in capitals or as written; the options are `headingCases`.
     * @param  PortalDesignOption             $shadow                How much the surfaces lift off the page.
     * @param  list<PortalDesignOption>       $shadows               The three depths.
     * @param  bool                           $cardBorder            Whether the two surface cards draw a hairline border.
     * @param  PortalDesignOption             $arrangement           Where the sidebar card sits against the main card.
     * @param  list<PortalDesignOption>       $arrangements          The four arrangements.
     * @param  PortalDesignOption             $contentWidth          How wide the page's column is.
     * @param  list<PortalDesignOption>       $contentWidths         The three widths.
     * @param  bool                           $stickySidebar         Whether the sidebar card stays in view while the page scrolls.
     * @param  PortalDesignOption             $planGrid              How many columns the plan cards fill.
     * @param  list<PortalDesignOption>       $planGrids             The three column settings.
     * @param  PortalDesignOption             $planDensity           How much room a plan card takes.
     * @param  list<PortalDesignOption>       $planDensities         The two densities.
     * @param  bool                           $plansHeading          Whether the plan list opens with its heading and subtitle.
     * @param  bool                           $metricsSubscribers    Whether the subscriber count shows, while the project shows metrics at all.
     * @param  bool                           $metricsFollowers      Whether the follower count shows, while the project shows metrics at all.
     * @param  int                            $metricsThreshold      The count a figure must reach before it shows; zero shows every figure.
     * @param  string|null                    $plansTitle            The heading over the plan list, or null for the built-in wording.
     * @param  string|null                    $plansSubtitle         The line under the plan list heading, or null for the built-in wording.
     * @param  string|null                    $labelLogin            The header's sign-in button, or null for the built-in wording.
     * @param  string|null                    $labelShare            The header's share button, or null for the built-in wording.
     * @param  string|null                    $labelSubscribe        The button on a plan that sells, or null for the built-in wording.
     * @param  string|null                    $checkoutTitle         The checkout heading, or null for the built-in wording.
     * @param  string|null                    $checkoutSubtitle      The line under the checkout heading, or null for the built-in wording.
     * @param  string|null                    $checkoutNote          The creator's sentence above the acceptance line, or null for the built-in wording.
     * @param  string|null                    $successTitle          The heading of the success screen, or null for the built-in wording.
     * @param  string|null                    $successMessage        The creator's thank-you under the confirmation, or null for the built-in wording.
     * @param  array<string, int>             $copyMax               The most characters each copy override may carry, by design key.
     * @param  string|null                    $perksTitle            The heading over the perks, or null for the built-in wording.
     * @param  list<string>                   $perks                 The perks in order.
     * @param  string|null                    $faqTitle              The heading over the questions, or null for the built-in wording.
     * @param  list<PortalFaqItemDetails>     $faq                   The questions in order, each with its answer.
     * @param  string|null                    $aboutName             The creator's name on the about card, or null for none.
     * @param  string|null                    $aboutBio              A few lines about the creator, or null for none.
     * @param  array<string, int>             $blockLimits           The most items and characters each block takes: `perks`, `perk`, `faq`, `faq_question`, `faq_answer`, `title`, `about_name`, `about_bio`.
     */
    public function __construct(
        public string $projectId,
        public string $projectName,
        public bool $hasDraft,
        public ?DateTimeImmutable $draftSavedAt,
        public ?string $previewUrl,
        public ?string $portalUrl,
        public PortalDesignOption $preset,
        public array $presets,
        public PortalDesignOption $colorMode,
        public array $colorModes,
        public string $accent,
        public ?string $accentSecondary,
        public string $accentSecondaryInUse,
        public string $background,
        public bool $grid,
        public string $surface,
        public string $text,
        public string $heading,
        public string $muted,
        public ?string $tagline,
        public int $taglineMax,
        public array $socialLinks,
        public int $socialLinksMax,
        public array $networks,
        public string $customNetwork,
        public array $sections,
        public PortalDesignOption $fontDisplay,
        public PortalDesignOption $fontBody,
        public array $fonts,
        public PortalDesignOption $headingCase,
        public array $headingCases,
        public PortalDesignOption $typeScale,
        public array $typeScales,
        public PortalDesignOption $radius,
        public array $radii,
        public PortalDesignOption $buttonStyle,
        public array $buttonStyles,
        public PortalDesignOption $buttonFill,
        public array $buttonFills,
        public PortalDesignOption $buttonCase,
        public PortalDesignOption $shadow,
        public array $shadows,
        public bool $cardBorder,
        public PortalDesignOption $arrangement,
        public array $arrangements,
        public PortalDesignOption $contentWidth,
        public array $contentWidths,
        public bool $stickySidebar,
        public PortalDesignOption $planGrid,
        public array $planGrids,
        public PortalDesignOption $planDensity,
        public array $planDensities,
        public bool $plansHeading,
        public bool $metricsSubscribers,
        public bool $metricsFollowers,
        public int $metricsThreshold,
        public ?string $plansTitle,
        public ?string $plansSubtitle,
        public ?string $labelLogin,
        public ?string $labelShare,
        public ?string $labelSubscribe,
        public ?string $checkoutTitle,
        public ?string $checkoutSubtitle,
        public ?string $checkoutNote,
        public ?string $successTitle,
        public ?string $successMessage,
        public array $copyMax,
        public ?string $perksTitle,
        public array $perks,
        public ?string $faqTitle,
        public array $faq,
        public ?string $aboutName,
        public ?string $aboutBio,
        public array $blockLimits,
    ) {}

    /**
     * @param   string              $value  An option's value.
     * @return  PortalDesignOption  The section with that value, or null when none has it.
     */
    public function section(string $value): ?PortalDesignOption
    {
        foreach ($this->sections as $section) {
            if ($section->value === $value) {
                return $section;
            }
        }

        return null;
    }

    /**
     * @param   string              $value  A network's value.
     * @return  PortalDesignOption  The network with that value, or null when none has it.
     */
    public function network(string $value): ?PortalDesignOption
    {
        foreach ($this->networks as $network) {
            if ($network->value === $value) {
                return $network;
            }
        }

        return null;
    }

    /**
     * @param   string              $value  A face's value.
     * @return  PortalDesignOption  The face with that value, or null when none has it.
     */
    public function font(string $value): ?PortalDesignOption
    {
        foreach ($this->fonts as $font) {
            if ($font->value === $value) {
                return $font;
            }
        }

        return null;
    }
}
