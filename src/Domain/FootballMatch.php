<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

class FootballMatch
{
    private int $homeScore = 0;
    private int $awayScore = 0;
    private DateTimeImmutable $startTime;

    public function __construct(private readonly Team $homeTeam, private readonly Team $awayTeam)
    {
        if ($homeTeam->getName() === $awayTeam->getName()) {
            throw new InvalidArgumentException('A team cannot play against itself.');
        }

        $this->startTime = new DateTimeImmutable();
    }

    public function getHomeScore(): int
    {
        return $this->homeScore;
    }

    public function getAwayScore(): int
    {
        return $this->awayScore;
    }

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getHomeTeam(): Team
    {
        return $this->homeTeam;
    }

    public function getAwayTeam(): Team
    {
        return $this->awayTeam;
    }

    public function totalScore(): int
    {
        return $this->homeScore + $this->awayScore;
    }

    public function updateScore(int $homeScore, int $awayScore): void
    {
        if ($homeScore < 0 || $awayScore < 0) {
            throw new InvalidArgumentException('Scores must be non-negative.');
        }

        $this->homeScore = $homeScore;
        $this->awayScore = $awayScore;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %d - %s %d',
            $this->homeTeam->getName(),
            $this->homeScore,
            $this->awayTeam->getName(),
            $this->awayScore,
        );
    }
}
