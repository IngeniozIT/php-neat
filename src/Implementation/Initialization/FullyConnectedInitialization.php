<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Initialization;

use IngeniozIT\Neat\Implementation\Interfaces\InitializationInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Genotype\Interfaces\GenotypeFactoryInterface;

class FullyConnectedInitialization extends NotConnectedInitialization implements InitializationInterface
{
    public function __invoke(
        PoolInterface &$pool,
        array $activationFunctions,
        array $aggregationFunctions
    ): void
    {
        parent::__invoke($pool, $activationFunctions, $aggregationFunctions);
        $this->addConnections($pool);
    }

    protected function addConnections(PoolInterface &$pool)
    {

    }
}
