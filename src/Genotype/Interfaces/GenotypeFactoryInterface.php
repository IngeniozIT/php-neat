<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface GenotypeFactoryInterface
{
    /**
     * Create a NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number.
     * @param  int $type Node type. NodeGenotype::TYPE_SENSOR, NodeGenotype::TYPE_OUTPUT or NodeGenotype::TYPE_HIDDEN.
     *
     * @return NodeGenotypeInterface
     */
    public function createNodeGenotype(int $type, ?int $innovNb = null): NodeGenotypeInterface;

    /**
     * Create a sensor NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number.
     *
     * @return NodeGenotypeInterface
     */
    public function createSensorNodeGenotype(?int $innovNb = null): NodeGenotypeInterface;

    /**
     * Create an output NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number.
     *
     * @return NodeGenotypeInterface
     */
    public function createOutputNodeGenotype(?int $innovNb = null): NodeGenotypeInterface;

    /**
     * Create hidden NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number.
     *
     * @return NodeGenotypeInterface
     */
    public function createHiddenNodeGenotype(?int $innovNb = null): NodeGenotypeInterface;

    /**
     * Create a NodeGeneInterface.
     *
     * @param  int $type Node type. NodeGenotype::TYPE_SENSOR, NodeGenotype::TYPE_OUTPUT or NodeGenotype::TYPE_HIDDEN.
     * @param  callable $activationFunction
     * @param  callable $aggregationFunction
     * @param  int|null $innovNb Innovation number.
     *
     * @return NodeGeneInterface [description]
     */
    public function createNodeGene(
        int $type,
        callable $activationFunction,
        callable $aggregationFunction,
        ?int $innovNb = null
    ): NodeGeneInterface;

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
        callable $aggregationFunction): NodeGeneInterface;
}
