<?php

declare(strict_types=1);

use App\Domain\Team;
use App\Event\ScoreBoard\FinishFootballGameEvent;
use App\Event\ScoreBoard\StartFootballGameEvent;
use App\Event\ScoreBoard\UpdateFootballScoreEvent;
use App\Listener\ScoreBoard\FinishFootballGame;
use App\Listener\ScoreBoard\StartFootballGame;
use App\Listener\ScoreBoard\UpdateFootballScore;
use App\Service\ScoreBoard\CupScoreBoard;
use PHPUnit\Framework\TestCase;

class CupScoreBoardTest extends TestCase
{
    private array $teams;
    private CupScoreBoard $cupScoreBoard;

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
            ],
        ];

        $this->cupScoreBoard = new CupScoreBoard([
            new StartFootballGame(),
            new UpdateFootballScore(),
            new FinishFootballGame(),
        ]);
    }

    public function testStartFootballGames(): void
    {
        foreach ($this->teams as $team) {
            $event = new StartFootballGameEvent($team['home'], $team['away']);
            $this->cupScoreBoard->handle($event);
        }

        $expectedSummaryLines = array_map(
            static fn ($team) => sprintf(
                '%s %d - %s %d',
                $team['home']->getName(),
                0,
                $team['away']->getName(),
                0,
            ),
            array_reverse($this->teams),
        );

        $summaryLines = $this->cupScoreBoard->summaryLines();
        $this->assertCount(count($this->teams), $summaryLines);
        $this->assertEquals($expectedSummaryLines, $summaryLines);
    }

    public function testUpdateScoreFootballGame(): void
    {
        foreach ($this->teams as $team) {
            $startEvent = new StartFootballGameEvent($team['home'], $team['away']);
            $updateScoreEvent = new UpdateFootballScoreEvent($team['home'], $team['away'], $team['homeScore'], $team['awayScore']);
            $this->cupScoreBoard->handle($startEvent);
            $this->cupScoreBoard->handle($updateScoreEvent);
        }

        $summaryLines = $this->cupScoreBoard->summaryLines();
        $this->assertCount(count($this->teams), $summaryLines);
        $this->assertEquals([
            'Uruguay 6 - Italy 6',
            'Spain 10 - Brazil 2',
            'Mexico 0 - Canada 5',
            'Argentina 3 - Australia 1',
            'Germany 2 - France 2',
        ], $summaryLines);
    }

    public function testFinishFootballGame(): void
    {
        foreach ($this->teams as $team) {
            $startEvent = new StartFootballGameEvent($team['home'], $team['away']);
            $updateScoreEvent = new UpdateFootballScoreEvent($team['home'], $team['away'], $team['homeScore'], $team['awayScore']);
            $this->cupScoreBoard->handle($startEvent);
            $this->cupScoreBoard->handle($updateScoreEvent);
        }

        $summaryLines = $this->cupScoreBoard->summaryLines();
        $this->assertCount(count($this->teams), $summaryLines);
        $this->assertEquals([
            'Uruguay 6 - Italy 6',
            'Spain 10 - Brazil 2',
            'Mexico 0 - Canada 5',
            'Argentina 3 - Australia 1',
            'Germany 2 - France 2',
        ], $summaryLines);

        $finishEvent = new FinishFootballGameEvent($this->teams[0]['home'], $this->teams[0]['away']);
        $this->cupScoreBoard->handle($finishEvent);
        $summaryLines = $this->cupScoreBoard->summaryLines();
        $this->assertCount(count($this->teams) - 1, $summaryLines);
        $this->assertEquals([
            'Uruguay 6 - Italy 6',
            'Spain 10 - Brazil 2',
            'Argentina 3 - Australia 1',
            'Germany 2 - France 2',
        ], $summaryLines);
    }

    public function testNegativeScoresAreRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $homeTeam = new Team('Mexico');
        $awayTeam = new Team('Canada');
        $startEvent = new StartFootballGameEvent($homeTeam, $awayTeam);
        $updateScoreEvent = new UpdateFootballScoreEvent($homeTeam, $awayTeam, -1, 0);
        $this->cupScoreBoard->handle($startEvent);
        $this->cupScoreBoard->handle($updateScoreEvent);
    }

    public function testTeamUsedForSeveralMatchesAtTheSameTimeFails(): void
    {
        $this->cupScoreBoard->handle(new StartFootballGameEvent(new Team('Uruguay'), new Team('France')));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Team 'Uruguay' is already playing a match.");

        $this->cupScoreBoard->handle(new StartFootballGameEvent(new Team('Germany'), new Team('Uruguay')));
    }

    public function testFinishingFreesTeamsSoTheyCanPlayAgain(): void
    {
        $home = new Team('Uruguay');
        $away = new Team('France');

        $this->cupScoreBoard->handle(new StartFootballGameEvent($home, $away));
        $this->cupScoreBoard->handle(new FinishFootballGameEvent($home, $away));

        $summaryLines = $this->cupScoreBoard->summaryLines();
        $this->assertCount(0, $summaryLines);

        $this->cupScoreBoard->handle(new StartFootballGameEvent($home, $away));

        $summaryLines = $this->cupScoreBoard->summaryLines();
        $this->assertCount(1, $summaryLines);
    }
}
