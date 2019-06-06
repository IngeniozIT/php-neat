<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface GenotypeFactoryInterface
{
    /**
     * Create a NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     * @param  int $type Node type. NodeGenotype::TYPE_SENSOR, NodeGenotype::TYPE_OUTPUT or NodeGenotype::TYPE_HIDDEN.
     *
     * @return NodeGenotypeInterface
     */
    public function createNodeGenotype(int $type, ?int $innovNb = null): NodeGenotypeInterface;

    /**
     * Create a sensor NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     *
     * @return NodeGenotypeInterface
     */
    public function createSensorNodeGenotype(?int $innovNb = null): NodeGenotypeInterface;

    /**
     * Create an output NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     *
     * @return NodeGenotypeInterface
     */
    public function createOutputNodeGenotype(?int $innovNb = null): NodeGenotypeInterface;

    /**
     * Create hidden NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     *
     * @return NodeGenotypeInterface
     */
    public function createHiddenNodeGenotype(?int $innovNb = null): NodeGenotypeInterface;

    /**
     * Create a NodeGeneInterface.
     *
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     * @param  int $type Node type. NodeGenotype::TYPE_SENSOR, NodeGenotype::TYPE_OUTPUT or NodeGenotype::TYPE_HIDDEN.
     * @param  callable $activationFunction
     * @param  callable $aggregationFunction
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

    /**
     * Create a ConnectGenotypeInterface.
     *
     * @param int $inId Innovation number of the input node.
     * @param int $outId Innovation number of the output node.
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     *
     * @return ConnectGenotypeInterface
     */
    public function createConnectGenotype(int $inId, int $outId, ?int $innovNb = null): ?ConnectGenotypeInterface;

    /**
     * Create a ConnectGeneInterface.
     *
     * @param int $inId Innovation number of the input node.
     * @param int $outId Innovation number of the output node.
     * @param float $weight Initial connection weight.
     * @param bool $disabled True if the connection is disabled, false otherwise.
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     *
     * @return ConnectGeneInterface
     */
    public function createConnectGene(
        int $inId,
        int $outId,
        float $weight,
        bool $disabled = false,
        ?int $innovNb = null
    ): ?ConnectGeneInterface;

    /**
     * Create a ConnectGeneInterface from a ConnectGenotypeInterface.
     *
     * @param  ConnectGenotypeInterface $connectGenotype
     * @param float $weight Initial connection weight.
     * @param bool $disabled True if the connection is disabled, false otherwise.
     *
     * @return ConnectGeneInterface
     */
    public function createConnectGeneFromConnectGenotype(
        ConnectGenotypeInterface $connectGenotype,
        float $weight,
        bool $disabled = false
    ): ConnectGeneInterface;
}
