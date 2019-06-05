<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Speciation;

use IngeniozIT\Neat\Implementation\Interfaces\SpeciationInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Math\KMeans;

class KmeansSpeciation implements SpeciationInterface
{
    public function __invoke(PoolInterface $pool): void
    {
        $vectors = $pool->vectors();

        $kmeans = new KMeans($vectors);
        $kmeans->classifyAndOptimize();

        foreach ($kmeans->clusters() as $speciesId => $agentsIds) {
            $pool->assignSpecies($speciesId, $agentsIds);
        }
    }
}
