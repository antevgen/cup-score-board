<?php

declare(strict_types=1);

namespace App\Listener\ScoreBoard;

use App\Domain\Collection\MatchCollection;
use App\Event\ScoreBoard\EventInterface;
use App\Event\ScoreBoard\UpdateFootballScoreEvent;
use App\Service\ScoreBoard\ScoreBoardInterface;

class UpdateFootballScore implements BoardInterface
{

    public function supports(EventInterface $event): bool
    {
        return $event instanceof UpdateFootballScoreEvent;
    }

    public function handle(EventInterface $event, MatchCollection $matches): void
    {
        // TODO: Implement handle() method.
    }
}
