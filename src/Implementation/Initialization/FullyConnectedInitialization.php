<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Initialization;

use IngeniozIT\Neat\Implementation\Interfaces\InitializationInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Genotype\Interfaces\GenotypeFactoryInterface;
use IngeniozIT\Math\Random;

class FullyConnectedInitialization extends NotConnectedInitialization implements InitializationInterface
{
    public function __invoke(
        PoolInterface $pool,
        array $activationFunctions,
        array $aggregationFunctions
    ): void
    {
        parent::__invoke($pool, $activationFunctions, $aggregationFunctions);
        $this->addConnections($pool, $activationFunctions, $aggregationFunctions);
    }

    protected function addConnections(
        PoolInterface $pool,
        array $activationFunctions,
        array $aggregationFunctions
    )
    {
        $genotypeFactory = $pool->genotypeFactory();
        $connectGenotypes = [];
        foreach ($pool->sensorGenes() as $sensorGene) {
            foreach ($pool->outputGenes() as $outputGene) {
                $connectGenotypes[] = $genotypeFactory->createConnectGenotype(
                    $sensorGene->innovNb(),
                    $outputGene->innovNb()
                );
            }
        }

        foreach ($pool->agents() as $agent) {
            foreach ($connectGenotypes as $connectGenotype) {
                $agent->addConnectGene(
                    $genotypeFactory->createConnectGeneFromConnectGenotype(
                        $connectGenotype,
                        Random::frand(-1, 1)
                    )
                );
            }
        }
    }
}
