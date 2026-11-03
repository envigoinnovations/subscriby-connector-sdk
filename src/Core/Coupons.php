<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CouponDetails;
use Subscriby\Connector\Data\CouponDraft;
use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Exceptions\CouponRefused;

/**
 * A project's coupons, as a connector's creator surface lists, creates, pauses and deletes them.
 *
 * A coupon is one code any number of members may redeem for money off; an
 * access code is one code for one person and lives on `AccessCodes`. Every
 * call takes the `CreatorRef` of the account talking and runs as that
 * creator with the dashboard's own rules, and every refusal reaches the
 * connector as `CouponRefused` with a stable reason.
 */
interface Coupons
{
    /**
     * @param   CreatorRef           $creator  The creator talking.
     * @param   ProjectRef           $project  The project.
     * @return  list<CouponDetails>  The project's coupons, newest first.
     *
     * @throws  CouponRefused  When the creator or the project is unknown.
     */
    public function listForProject(CreatorRef $creator, ProjectRef $project): array;

    /**
     * @param   CreatorRef     $creator  The creator talking.
     * @param   ProjectRef     $project  The project.
     * @param   CouponDraft    $draft    The coupon: a code, a percentage or a fixed amount with its currency, and what narrows it.
     * @return  CouponDetails  The coupon as written, its code upper-cased.
     *
     * @throws  CouponRefused  When the creator or the project is unknown, or a field is refused.
     */
    public function create(CreatorRef $creator, ProjectRef $project, CouponDraft $draft): CouponDetails;

    /**
     * @param   CreatorRef     $creator   The creator talking.
     * @param   string         $couponId  The coupon.
     * @param   bool           $active    True to let it be redeemed, false to pause it.
     * @return  CouponDetails  The coupon as written.
     *
     * @throws  CouponRefused  When the creator or the coupon is unknown.
     */
    public function setActive(CreatorRef $creator, string $couponId, bool $active): CouponDetails;

    /**
     * @param  CreatorRef  $creator   The creator talking.
     * @param  string      $couponId  The coupon.
     *
     * @throws  CouponRefused  When the creator or the coupon is unknown, or the core will not delete it.
     */
    public function delete(CreatorRef $creator, string $couponId): void;
}
