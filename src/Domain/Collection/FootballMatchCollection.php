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
        $home = $item->getHomeTeam();
        $away = $item->getAwayTeam();
        $name = $this->getFootballMatchName($home, $away);
        if ($this->has($name)) {
            throw new RuntimeException('A match between these teams already exists.');
        }

        $activeTeams = $this->getActiveTeams();

        if (isset($activeTeams[$home->getName()])) {
            throw new RuntimeException(\sprintf("Team '%s' is already playing a match.", $home->getName()));
        }
        if (isset($activeTeams[$away->getName()])) {
            throw new RuntimeException(\sprintf("Team '%s' is already playing a match.", $away->getName()));
        }

        $this->items[$name] = $item;
    }

    public function get(Team $homeTeam, Team $awayTeam): FootballMatch
    {
        $name = $this->getFootballMatchName($homeTeam, $awayTeam);
        if (!$this->has($name)) {
            throw new OutOfBoundsException('A match between these teams do not exists.');
        }

        return $this->items[$name];
    }

    public function remove(Team $homeTeam, Team $awayTeam): void
    {
        $name = $this->getFootballMatchName($homeTeam, $awayTeam);
        if (!$this->has($name)) {
            throw new OutOfBoundsException('A match between these teams do not exists.');
        }
        unset($this->items[$name]);
    }

    private function getFootballMatchName(Team $homeTeam, Team $awayTeam): string
    {
        return \sprintf('%s-%s', $homeTeam->getName(), $awayTeam->getName());
    }

    private function getActiveTeams(): array
    {
        $activeTeams = [];
        foreach ($this->items as $match) {
            $home = $match->getHomeTeam();
            $away = $match->getAwayTeam();

            if (!isset($activeTeams[$home->getName()])) {
                $activeTeams[$home->getName()] = $home;
            }
            if (!isset($activeTeams[$away->getName()])) {
                $activeTeams[$away->getName()] = $away;
            }
        }

        return $activeTeams;
    }
}
