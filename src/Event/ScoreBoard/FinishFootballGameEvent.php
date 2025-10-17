<?php

declare(strict_types=1);

namespace App\Event\ScoreBoard;

use App\Domain\Team;

class FinishFootballGameEvent implements EventInterface
{
    public function __construct(public Team $homeTeam, public Team $awayTeam) {
    }
}
