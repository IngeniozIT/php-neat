<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Initialization;

use IngeniozIT\Neat\Implementation\Interfaces\InitializationInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Genotype\Interfaces\GenotypeFactoryInterface;
use IngeniozIT\Meth\Random;

class NotConnectedInitialization implements InitializationInterface
{
    public function __invoke(
        PoolInterface &$pool,
        array $activationFunctions,
        array $aggregationFunctions
    ): void
    {
        $sensorGenes = $pool->sensorGenes();
        $outputGenes = $pool->outputGenes();
        $genotypeFactory = $pool->genotypeFactory();
        $agentFactory = $pool->agentFactory();
        while (count($pool) < $pool->populationSize()) {
            $nodeGenes = [];

            foreach ($sensorGenes as $sensorGene) {
                $nodeGenes[] = $genotypeFactory->createNodeGeneFromNodeGenotype(
                    $sensorGene,
                    $activationFunctions[rand() % count($activationFunctions)],
                    $aggregationFunctions[rand() % count($aggregationFunctions)]
                );
            }
            foreach ($outputGenes as $outputGene) {
                $nodeGenes[] = $genotypeFactory->createNodeGeneFromNodeGenotype(
                    $outputGene,
                    $activationFunctions[rand() % count($activationFunctions)],
                    $aggregationFunctions[rand() % count($aggregationFunctions)]
                );
            }

            $pool->addAgent($agentFactory->createAgent($nodeGenes, []));
        }
    }
}
