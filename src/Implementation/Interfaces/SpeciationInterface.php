<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Interfaces;

use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;

interface SpeciationInterface
{
    /**
     * Classify a PoolInterface's agents into species.
     *
     * @param  PoolInterface $pool
     */
    public function __invoke(PoolInterface $pool): void;
}
