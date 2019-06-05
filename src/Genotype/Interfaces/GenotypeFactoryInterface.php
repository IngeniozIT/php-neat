<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface GenotypeFactoryInterface
{
    /**
     * Create a sensor NodeGenotypeInterface.
     *
     * @param  int $innovNb Innovation number.
     *
     * @return NodeGenotypeInterface
     */
    public function createSensorNodeGenotype(int $innovNb): NodeGenotypeInterface;

    /**
     * Create an output NodeGenotypeInterface.
     *
     * @param  int $innovNb Innovation number.
     *
     * @return NodeGenotypeInterface
     */
    public function createOutputNodeGenotype(int $innovNb): NodeGenotypeInterface;

    /**
     * Create hidden NodeGenotypeInterface.
     *
     * @param  int $innovNb Innovation number.
     *
     * @return NodeGenotypeInterface
     */
    public function createHiddenNodeGenotype(int $innovNb): NodeGenotypeInterface;

    /**
     * Create a NodeGenotypeInterface.
     *
     * @param  int $innovNb Innovation number.
     * @param  int $type Node type. NodeGenotype::TYPE_SENSOR, NodeGenotype::TYPE_OUTPUT or NodeGenotype::TYPE_HIDDEN.
     *
     * @return NodeGenotypeInterface
     */
    public function createNodeGenotype(int $innovNb, int $type): NodeGenotypeInterface;

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
    ): NodeGeneInterface;
}
