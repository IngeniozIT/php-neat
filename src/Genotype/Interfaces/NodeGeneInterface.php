<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface NodeGeneInterface extends NodeGenotypeInterface
{
    /**
     * Get the aggregation function of the node gene.
     * The aggregation function should look like "aggregationFunction(array|iterable $values): int|float".
     *
     * @return callable
     */
    public function aggregationFunction(): callable;

    /**
     * Set the aggregation function of the node gene.
     * The aggregation function should look like "aggregationFunction(array|iterable $values): int|float".
     *
     * @param callable $aggrFunction
     */
    public function setAggregationFunction(callable $aggrFunction): void;

    /**
     * Get the activation function of the node gene.
     * The activation function should look like "aggregationFunction(float $value): int|float".
     *
     * @return callable
     */
    public function activationFunction(): callable;

    /**
     * Set the activation function of the node gene.
     * The activation function should look like "aggregationFunction(float $value): int|float".
     *
     * @param callable $actFunction
     */
    public function setActivationFunction(callable $actFunction): void;
}
