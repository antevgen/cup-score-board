<?php

declare(strict_types=1);

namespace App\Listener\ScoreBoard;

use App\Domain\Collection\FootballMatchCollection;
use App\Domain\Collection\MatchCollection;
use App\Event\ScoreBoard\EventInterface;
use App\Event\ScoreBoard\FinishFootballGameEvent;

class FinishFootballGame implements BoardInterface
{
    public function supports(EventInterface $event): bool
    {
        return $event instanceof FinishFootballGameEvent;
    }

    /**
     * @param FinishFootballGameEvent $event
     */
    public function handle(EventInterface $event, FootballMatchCollection|MatchCollection $matches): void
    {
        $matches->remove($event->homeTeam, $event->awayTeam);
    }
}
