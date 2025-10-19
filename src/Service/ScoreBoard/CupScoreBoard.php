<?php

declare(strict_types=1);

namespace App\Service\ScoreBoard;

use App\Domain\Collection\FootballMatchCollection;
use App\Domain\FootballMatch;
use App\Event\ScoreBoard\EventInterface;
use App\Listener\ScoreBoard\BoardInterface;
use InvalidArgumentException;

class CupScoreBoard implements ScoreBoardInterface
{
    private FootballMatchCollection $matches;

    /** @param iterable<BoardInterface> $listeners */
    public function __construct(private readonly iterable $listeners)
    {
        $this->matches = new FootballMatchCollection();
    }

    public function handle(EventInterface $event): void
    {
        $handled = false;
        foreach ($this->listeners as $listener) {
            if ($listener->supports($event)) {
                $listener->handle($event, $this->matches);
                $handled = true;
                break;
            }
        }

        if (!$handled) {
            throw new InvalidArgumentException(\sprintf('No listener for event: %s', $event::class));
        }
    }

    /** @return FootballMatchCollection<FootballMatch> */
    public function summary(): FootballMatchCollection
    {
        return $this->matches->sort(
            function (FootballMatch $a, FootballMatch $b): int {
                $cmp = $b->totalScore() <=> $a->totalScore();
                if ($cmp !== 0) {
                    return $cmp;
                }

                return $b->getStartTime() <=> $a->getStartTime();
            }
        );
    }

    /** @return array<int, string> */
    public function summaryLines(): array
    {
        return $this->summary()->map(
            fn (FootballMatch $footballMatch) => (string) $footballMatch,
        )->values();
    }
}
