<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Interfaces;

use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;

interface SpeciationInterface
{
    public function __invoke(PoolInterface $pool): void;
}
