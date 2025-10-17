<?php

declare(strict_types=1);

namespace App\Listener\ScoreBoard;

use App\Domain\Collection\FootballMatchCollection;
use App\Domain\Collection\MatchCollection;
use App\Event\ScoreBoard\EventInterface;
use App\Event\ScoreBoard\UpdateFootballScoreEvent;

class UpdateFootballScore implements BoardInterface
{

    public function supports(EventInterface $event): bool
    {
        return $event instanceof UpdateFootballScoreEvent;
    }

    /**
     * @param UpdateFootballScoreEvent $event
     */
    public function handle(EventInterface $event, FootballMatchCollection|MatchCollection $matches): void
    {
        $matches->get($event->homeTeam, $event->awayTeam)->updateScore($event->homeScore, $event->awayScore);
    }
}
