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
    /**
     * Create a sensor NodeGenotypeInterface.
     *
     * @param  int $innovNb Innovation number.
     *
     * @return NodeGenotypeInterface
     */
    public function createSensorNodeGenotype(int $innovNb): NodeGenotypeInterface
    {
        return $this->createNodeGenotype($innovNb, NodeGenotype::TYPE_SENSOR);
    }

    /**
     * Create an output NodeGenotypeInterface.
     *
     * @param  int $innovNb Innovation number.
     *
     * @return NodeGenotypeInterface
     */
    public function createOutputNodeGenotype(int $innovNb): NodeGenotypeInterface
    {
        return $this->createNodeGenotype($innovNb, NodeGenotype::TYPE_OUTPUT);
    }

    /**
     * Create hidden NodeGenotypeInterface.
     *
     * @param  int $innovNb Innovation number.
     *
     * @return NodeGenotypeInterface
     */
    public function createHiddenNodeGenotype(int $innovNb): NodeGenotypeInterface
    {
        return $this->createNodeGenotype($innovNb, NodeGenotype::TYPE_HIDDEN);
    }

    /**
     * Create a NodeGenotypeInterface.
     *
     * @param  int $innovNb Innovation number.
     * @param  int $type Node type. NodeGenotype::TYPE_SENSOR, NodeGenotype::TYPE_OUTPUT or NodeGenotype::TYPE_HIDDEN.
     *
     * @return NodeGenotypeInterface
     */
    public function createNodeGenotype(int $innovNb, int $type): NodeGenotypeInterface
    {
        return new NodeGenotype($innovNb, $type);
    }

    /**
     * Create a NodeGeneInterface from a NodeGenotypeInterface.
     *
     * @param  NodeGenotypeInterface $nodeGenotype
     * @param  callable $activationFunction
     * @param  callable $aggregationFunction
     *
     * @return NodeGeneInterface
     */
    public function createNodeGeneFromNodeGenotype(
        NodeGenotypeInterface $nodeGenotype,
        callable $activationFunction,
        callable $aggregationFunction): NodeGeneInterface
    {
        return $this->createNodeGene($nodeGenotype->innovNb(), $nodeGenotype->type(), $activationFunction, $aggregationFunction);
    }

    /**
     * Create a NodeGeneInterface.
     *
     * @param  int $innovNb Innovation number.
     * @param  int $type Node type. NodeGenotype::TYPE_SENSOR, NodeGenotype::TYPE_OUTPUT or NodeGenotype::TYPE_HIDDEN.
     * @param  callable $activationFunction
     * @param  callable $aggregationFunction
     *
     * @return NodeGeneInterface [description]
     */
    public function createNodeGene(
        int $innovNb,
        int $type,
        callable $activationFunction,
        callable $aggregationFunction
    ): NodeGeneInterface
    {
        return new NodeGene($innovNb, $type, $activationFunction, $aggregationFunction);
    }
}
