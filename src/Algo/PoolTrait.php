<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Algo;

trait PoolTrait
{
    protected $agents = [];
    protected $iteratorIndex = 0;

    public function count(): int
    {
        return count($this->agents);
    }

    public function current()
    {
        return $this->agents[$this->iteratorIndex];
    }

    public function key()
    {
        return $this->iteratorIndex;
    }

    public function next(): void
    {
        ++$this->iteratorIndex;
    }

    public function rewind(): void
    {
        $this->iteratorIndex = 0;
    }

    public function valid(): bool
    {
        return isset($this->agents[$this->iteratorIndex]);
    }
}
