<?php

declare(strict_types=1);

namespace App\Listener\ScoreBoard;

use App\Domain\Collection\MatchCollection;
use App\Event\ScoreBoard\EventInterface;

interface BoardInterface
{
    public function supports(EventInterface $event): bool;
    public function handle(EventInterface $event, MatchCollection $matches): void;
}
