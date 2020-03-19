<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use \Countable;
use \ArrayIterator;

abstract class Collection implements Countable
{
    /**
     * @var array
     */
    protected $aggregates = [];

    /**
     * @param object $aggregate
     */
    protected function append(object $aggregate): void
    {
        array_push($this->aggregates, $aggregate);
    }

    /**
     * @param array $aggregates
     */
    public function fromArray(array $aggregates): void
    {
        foreach ($aggregates as $aggregate) {
            $this->add($aggregate);
        }
    }

    /**
     * @return array
     */
    public function getAggregates(): array
    {
        return $this->aggregates;
    }

    /**
     * @return ArrayIterator
     */
    public function getAggregatesIterator(): ArrayIterator
    {
        return new ArrayIterator($this->aggregates);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->aggregates);
    }

    /**
     * @param callable $filter
     */
    protected function filter(callable $filter): self
    {   
        $filtered   = array_filter($this->aggregates, $filter);
        $collection = new static();
        
        if (!count($filtered)) {
            return $collection;
        }

        $collection->fromArray($filtered);
        return $collection;
    }

    /**
     * @param callable $mapper
     */
    protected function map(callable $mapper): self
    {
        $mapped     = array_map($mapper, $this->aggregates);
        $collection = new static();

        if (!count($mapped)) {
            return $collection;
        }

        $collection->fromArray($mapped);
        return $collection;
    }

    /**
     * @param callable $reducer
     * @param mixed $initial
     * @return mixed
     */
    protected function reduce(callable $reducer, $initial = null)
    {
        return array_reduce($this->aggregates, $reducer, $initial);
    }
}
