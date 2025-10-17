<?php

declare(strict_types=1);

use App\Domain\Team;
use App\Event\ScoreBoard\StartFootballGameEvent;
use App\Listener\ScoreBoard\FinishFootballGame;
use App\Listener\ScoreBoard\StartFootballGame;
use App\Listener\ScoreBoard\UpdateFootballScore;
use App\Service\ScoreBoard\CupScoreBoard;
use PHPUnit\Framework\TestCase;

class CupScoreBoardTest extends TestCase
{
    public function testStartFootballGame(): void
    {
        $homeTeam = new Team('Mexico');
        $awayTeam = new Team('Canada');
        $event = new StartFootballGameEvent($homeTeam, $awayTeam);

        $listeners = [
            new StartFootballGame(),
            new UpdateFootballScore(),
            new FinishFootballGame(),
        ];

        $cupScoreBoard = new CupScoreBoard($listeners);
        $cupScoreBoard->handle($event);

        $summaryLines = $cupScoreBoard->summaryLines();
        $this->assertCount(1, $summaryLines);
        $this->assertContains('Mexico 0 - Canada 0', $summaryLines);
    }
}
