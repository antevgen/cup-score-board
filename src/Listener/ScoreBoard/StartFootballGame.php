<?php

declare(strict_types=1);

namespace App\Listener\ScoreBoard;

use App\Domain\Collection\MatchCollection;
use App\Domain\FootballMatch;
use App\Event\ScoreBoard\EventInterface;
use App\Event\ScoreBoard\StartFootballGameEvent;

class StartFootballGame implements BoardInterface
{

    public function supports(EventInterface $event): bool
    {
        return $event instanceof StartFootballGameEvent;
    }

    /**
     * @param StartFootballGameEvent $event
     */
    public function handle(EventInterface $event, MatchCollection $matches): void
    {
        $match = new FootballMatch($event->homeTeam, $event->awayTeam);
        $matches->add($match);
    }
}
