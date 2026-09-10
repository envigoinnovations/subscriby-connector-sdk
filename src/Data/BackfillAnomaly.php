<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One class of legacy rows a data migration could not map cleanly.
 *
 * Printed by the dry run so production surprises are decided on paper: the
 * kind is a stable machine word, the description says what the rows are and
 * what the migration did with them, and the samples let an operator look a
 * few up.
 */
final readonly class BackfillAnomaly
{
    /**
     * @param  string        $kind         A stable machine word, such as `member_without_chat_row`.
     * @param  string        $description  What the rows are and how the migration treats them.
     * @param  int           $count        How many rows fall in this class.
     * @param  list<string>  $samples      A few ids to look up.
     */
    public function __construct(
        public string $kind,
        public string $description,
        public int $count,
        public array $samples = [],
    ) {}
}
