<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface;

class NodeGene extends NodeGenotype implements NodeGeneInterface
{
    /**
     * @var callable
     */
    protected $activationFunction;

    /**
     * @var callable
     */
    protected $aggregationFunction;

    /**
     * Constructor.
     *
     * @param int $innovNb Innovation number
     * @param int $type The node type. Either NodeGenotype::NODE_SENSOR, NodeGenotype::NODE_OUTPUT or
     * NodeGenotype::NODE_HIDDEN.
     * @param callable $aggregationFunction The aggregation function that the node will use.
     * @param callable $activationFunction The activation function that the node will use.
     */
    public function __construct(int $innovNb, int $type, callable $activationFunction, callable $aggregationFunction)
    {
        parent::__construct($innovNb, $type);

        $this->activationFunction = $activationFunction;
        $this->aggregationFunction = $aggregationFunction;
    }

    /**
     * Get the activation function of the node gene.
     * The activation function should look like "aggregationFunction(float $value): int|float".
     *
     * @return callable
     */
    public function activationFunction(): callable
    {
        return $this->activationFunction;
    }

    /**
     * Set the activation function of the node gene.
     * The activation function should look like "aggregationFunction(float $value): int|float".
     *
     * @param callable $actFunction
     */
    public function setActivationFunction(callable $actFunction): void
    {
        $this->activationFunction = $actFunction;
    }

    /**
     * Get the aggregation function of the node gene.
     * The aggregation function should look like "aggregationFunction(array|iterable $values): int|float".
     *
     * @return callable
     */
    public function aggregationFunction(): callable
    {
        return $this->aggregationFunction;
    }

    /**
     * Set the aggregation function of the node gene.
     * The aggregation function should look like "aggregationFunction(array|iterable $values): int|float".
     *
     * @param callable $aggrFunction
     */
    public function setAggregationFunction(callable $aggrFunction): void
    {
        $this->aggregationFunction = $aggrFunction;
    }
}
