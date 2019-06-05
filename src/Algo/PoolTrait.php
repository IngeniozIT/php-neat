<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Algo;

trait PoolTrait
{
    protected $agents = [];

    public function count(): int
    {
        return count($this->agents);
    }

    public function current()
    {
        return current($this->agents);
    }

    public function key()
    {
        return key($this->agents);
    }

    public function next(): void
    {
        next($this->agents);
    }

    public function rewind(): void
    {
        reset($this->agents);
    }

    public function valid(): bool
    {
        return array_key_exists($this->key(), $this->agents);
    }
}
