<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One page of a date's holders, with the date they hold.
 */
final readonly class PassHolderPage
{
    /**
     * @param  PassRosterDate    $date     The date, with its roll call.
     * @param  list<PassHolder>  $holders  The page's holders, in the order they bought.
     * @param  int               $page     Which page this is, one-based.
     * @param  int               $perPage  How many holders a page carries.
     * @param  int               $total    How many holders the date has in all.
     */
    public function __construct(
        public PassRosterDate $date,
        public array $holders,
        public int $page,
        public int $perPage,
        public int $total,
    ) {}

    /**
     * @return  int  The last page's number, never below one.
     */
    public function lastPage(): int
    {
        return max(1, (int) ceil($this->total / max(1, $this->perPage)));
    }

    /**
     * @return  bool  Whether a page comes before this one.
     */
    public function hasPrevious(): bool
    {
        return $this->page > 1;
    }

    /**
     * @return  bool  Whether a page comes after this one.
     */
    public function hasNext(): bool
    {
        return $this->page < $this->lastPage();
    }
}
