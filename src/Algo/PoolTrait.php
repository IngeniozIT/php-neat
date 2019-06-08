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
}
