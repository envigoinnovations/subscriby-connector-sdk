<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A project's live Referral Program as a connector may describe it to a member.
 *
 * The words are the core's: what a referrer earns, the one-sentence offer,
 * what a friend receives, are phrased once by the core in the member's
 * language and handed over ready to print, so the bot, the portal and the
 * dashboard spell the same programme the same way and a connector never
 * composes a reward from its parts.
 */
final readonly class ReferralProgramSummary
{
    /**
     * @param  string        $id                  The programme row's UUID.
     * @param  string        $projectId           The project that runs it.
     * @param  string        $projectName         The project's name, for a share message.
     * @param  bool          $paysCash            Whether referrers earn money rather than membership days.
     * @param  bool          $customersOnly       Whether only members with an active membership may join.
     * @param  bool          $approvalRequired    Whether the creator approves each affiliate before their code attributes.
     * @param  int           $windowDays          How many days a referred friend stays attributed after arriving.
     * @param  string        $rewardLabel         What a referrer earns, in words ("7 free days of membership", "20% of each payment").
     * @param  string        $pitch               The one-sentence offer a member reads before joining.
     * @param  list<string>  $termsLines          The programme's terms as an affiliate is told them, one sentence a line: what they earn, what their friends get, how long a friend stays theirs and, on a cash programme, when a commission is payable. The same lines the core's approval and change notices carry.
     * @param  string|null   $welcomeSentence     What a friend is told on arrival about their own reward, or null when there is none.
     * @param  string|null   $terms               The creator's own terms, or null when they wrote none.
     * @param  string|null   $payoutDetailsLabel  The question affiliates answer so the creator can pay them ("PayPal email"), or null when the creator asked none; a cash programme without one takes no details.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $projectName,
        public bool $paysCash,
        public bool $customersOnly,
        public bool $approvalRequired,
        public int $windowDays,
        public string $rewardLabel,
        public string $pitch,
        public array $termsLines,
        public ?string $welcomeSentence,
        public ?string $terms,
        public ?string $payoutDetailsLabel,
    ) {}
}
