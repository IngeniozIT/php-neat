<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Speciation;

use IngeniozIT\Neat\Implementation\Interfaces\SpeciationInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Math\KMeans;

/**
 * A speciation function that uses the k-means algorithm.
 */
class KmeansSpeciation implements SpeciationInterface
{
    /**
     * Classify a PoolInterface's agents into species.
     *
     * @param  PoolInterface $pool
     */
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
