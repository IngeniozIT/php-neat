<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Selection;

use IngeniozIT\Neat\Implementation\Interfaces\SelectionInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Math\Random;

class OriginalSelection implements SelectionInterface
{
    public function __invoke(PoolInterface $pool): void
    {
        $popSize = $pool->populationSize();

        $i = 0;
        foreach ($pool as $agentId => $agent) {
            if (Random::frand() <= $i++ / $popSize) {
                echo 'Removing agent ', $i, PHP_EOL;
                $pool->removeAgent($agentId);
            }
        }
    }
}
