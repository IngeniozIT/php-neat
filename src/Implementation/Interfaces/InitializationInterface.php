<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Interfaces;

use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Genotype\Interfaces\GenotypeFactoryInterface;

interface InitializationInterface
{
    public function __invoke(
        PoolInterface &$pool,
        array $activationFunctions,
        array $aggregationFunctions
    );
}
