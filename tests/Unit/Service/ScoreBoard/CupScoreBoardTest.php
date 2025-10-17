<?php

declare(strict_types=1);

use App\Domain\Team;
use App\Event\ScoreBoard\StartFootballGameEvent;
use App\Event\ScoreBoard\UpdateFootballScoreEvent;
use App\Listener\ScoreBoard\BoardInterface;
use App\Listener\ScoreBoard\FinishFootballGame;
use App\Listener\ScoreBoard\StartFootballGame;
use App\Listener\ScoreBoard\UpdateFootballScore;
use App\Service\ScoreBoard\CupScoreBoard;
use PHPUnit\Framework\TestCase;

class CupScoreBoardTest extends TestCase
{
    /** @var array<BoardInterface> */
    private array $listeners;

    private array $teams;

    protected function setUp(): void
    {
        $this->teams = [
            [
                'home' => new Team('Mexico'),
                'away' => new Team('Canada'),
                'homeScore' => 0,
                'awayScore' => 5,
            ],
            [
                'home' => new Team('Spain'),
                'away' => new Team('Brazil'),
                'homeScore' => 10,
                'awayScore' => 2,
            ],
            [
                'home' => new Team('Germany'),
                'away' => new Team('France'),
                'homeScore' => 2,
                'awayScore' => 2,
            ],
            [
                'home' => new Team('Uruguay'),
                'away' => new Team('Italy'),
                'homeScore' => 6,
                'awayScore' => 6,
            ],
            [
                'home' => new Team('Argentina'),
                'away' => new Team('Australia'),
                'homeScore' => 3,
                'awayScore' => 1,
            ]
        ];

        $this->listeners = [
            new StartFootballGame(),
            new UpdateFootballScore(),
            new FinishFootballGame(),
        ];
    }

    public function testStartFootballGames(): void
    {
        $cupScoreBoard = new CupScoreBoard($this->listeners);
        foreach ($this->teams as $team) {
            $event = new StartFootballGameEvent($team['home'], $team['away']);
            $cupScoreBoard->handle($event);
        }

        $expectedSummaryLines = array_map(
            static fn($team) => sprintf(
                '%s %d - %s %d',
                $team['home']->getName(),
                0,
                $team['away']->getName(),
                0,
            ),
            array_reverse($this->teams),
        );

        $summaryLines = $cupScoreBoard->summaryLines();
        $this->assertCount(count($this->teams), $summaryLines);
        $this->assertEquals($expectedSummaryLines, $summaryLines);
    }

    public function testUpdateScoreFootballGame(): void
    {
        $cupScoreBoard = new CupScoreBoard($this->listeners);
        foreach ($this->teams as $team) {
            $startEvent = new StartFootballGameEvent($team['home'], $team['away']);
            $updateScoreEvent = new UpdateFootballScoreEvent($team['home'], $team['away'], $team['homeScore'], $team['awayScore']);
            $cupScoreBoard->handle($startEvent);
            $cupScoreBoard->handle($updateScoreEvent);
        }

        $summaryLines = $cupScoreBoard->summaryLines();
        $this->assertCount(count($this->teams), $summaryLines);
        $this->assertEquals([
            'Uruguay 6 - Italy 6',
            'Spain 10 - Brazil 2',
            'Mexico 0 - Canada 5',
            'Argentina 3 - Australia 1',
            'Germany 2 - France 2',
        ], $summaryLines);
    }
}
