<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Speciation;

use IngeniozIT\Neat\Implementation\Interfaces\SpeciationInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;

class OriginalSpeciation implements SpeciationInterface
{
    public function __invoke(PoolInterface $pool): void
    {
        foreach ($pool as $agent) {
            $agent->setSpecies(rand() % 3);
        }
    }
}
