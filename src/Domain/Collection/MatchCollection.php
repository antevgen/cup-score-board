<?php

declare(strict_types=1);

namespace App\Domain\Collection;

use ArgumentCountError;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 */
class MatchCollection implements IteratorAggregate
{
    /**
     * @param array<int, TValue> $items
     */
    public function __construct(protected array $items = [])
    {
    }

    public function has(string $name): bool
    {
        return isset($this->items[$name]);
    }

    /**
     * @param TValue $item
     */
    public function add($item): void
    {
        $this->items[] = $item;
    }

    public function sort(callable $callback): static
    {
        $items = $this->items;
        usort($items, $callback);

        return new static($items);
    }

    public function map(callable $callback): static
    {
        $keys = array_keys($this->items);

        try {
            $items = array_map($callback, $this->items, $keys);
        } catch (ArgumentCountError) {
            $items = array_map($callback, $this->items);
        }

        return new static(array_combine($keys, $items));
    }

    /**
     * @return array<int, TValue>
     */
    public function values(): array
    {
        return array_values($this->items);
    }

    /**
     * @return array<int, TValue>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
