<?php

declare(strict_types=1);

namespace App\Domain\Collection;

use App\Domain\FootballMatch;
use App\Domain\Team;
use OutOfBoundsException;
use RuntimeException;

class FootballMatchCollection extends MatchCollection
{
    public function add($item): void
    {
        $name = $this->getFootballMatchName($item->getHomeTeam(), $item->getAwayTeam());
        if ($this->has($name)) {
            throw new RuntimeException('A match between these teams already exists.');
        }

        $this->items[$name] = $item;
    }

    public function get(Team $homeTeam, Team $awayTeam): FootballMatch
    {
        $name = $this->getFootballMatchName($homeTeam, $awayTeam);
        if (! $this->has($name)) {
            throw new OutOfBoundsException('A match between these teams do not exists.');
        }

        return $this->items[$name];
    }

    public function remove(Team $homeTeam, Team $awayTeam): void
    {
        $name = $this->getFootballMatchName($homeTeam, $awayTeam);
        if (! $this->has($name)) {
            throw new OutOfBoundsException('A match between these teams do not exists.');
        }
        unset($this->items[$name]);
    }

    private function getFootballMatchName(Team $homeTeam, Team $awayTeam): string
    {
        return sprintf('%s-%s', $homeTeam->getName(), $awayTeam->getName());
    }
}
