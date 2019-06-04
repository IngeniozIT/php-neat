<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\GenotypeFactoryInterface;
use IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface;
use IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface;
use IngeniozIT\Neat\Genotype\NodeGenotype;
use IngeniozIT\Neat\Genotype\NodeGene;

class GenotypeFactory implements GenotypeFactoryInterface
{
    public function createSensorNodeGenotype(int $innovNb): NodeGenotypeInterface
    {
        return $this->createNodeGenotype($innovNb, NodeGenotype::TYPE_SENSOR);
    }

    public function createOutputNodeGenotype(int $innovNb): NodeGenotypeInterface
    {
        return $this->createNodeGenotype($innovNb, NodeGenotype::TYPE_OUTPUT);
    }

    public function createNodeGenotype(int $innovNb, int $type): NodeGenotypeInterface
    {
        return new NodeGenotype($innovNb, $type);
    }

    public function createNodeGeneFromNodeGenotype(NodeGenotypeInterface $nodeGenotype, callable $activationFunction, callable $aggregationFunction) {
        return $this->createNodeGene($nodeGenotype->innovNb(), $nodeGenotype->type(), $activationFunction, $aggregationFunction);
    }

    public function createNodeGene(int $innovNb, int $type, callable $activationFunction, callable $aggregationFunction): NodeGeneInterface
    {
        return new NodeGene($innovNb, $type, $activationFunction, $aggregationFunction);
    }
}
