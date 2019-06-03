<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Algo;

use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Exceptions\InvalidArgumentException;
use IngeniozIT\Neat\Genotype\Interfaces\GenotypeFactoryInterface;
use IngeniozIT\Neat\Agents\Interfaces\AgentFactoryInterface;

class Pool implements PoolInterface
{
    protected $nbInputs;
    protected $nbOutputs;
    protected $populationSize;
    protected $genotypeFactory;
    protected $agentFactory;

    public function __construct(
        int $nbInputs,
        int $nbOutputs,
        $populationSize,
        callable $initializationMethod,
        array $activationFunctions,
        array $aggregationFunctions,
        array $defaultActivationFunctions,
        array $defaultAggregationFunctions,
        GenotypeFactoryInterface $genotypeFactory,
        AgentFactoryInterface $agentFactory
    ) {
        if ($nbInputs < 1) {
            throw new InvalidArgumentException('Number of inputs must be positive.');
        }
        if ($nbOutputs < 1) {
            throw new InvalidArgumentException('Number of outputs must be positive.');
        }
        if (\is_int($populationSize)) {
            if ($populationSize < 1) {
                throw new InvalidArgumentException('Population size must be positive.');
            }
        } elseif (!is_callable($populationSize)) {
            throw new InvalidArgumentException('Population size must either be int or callable.');
        }
        foreach ($activationFunctions as $activationFunction) {
            if (!is_callable($activationFunction)) {
                throw new InvalidArgumentException('Activation functions must be callables.');
            }
        }
        foreach ($aggregationFunctions as $aggregationFunction) {
            if (!is_callable($aggregationFunction)) {
                throw new InvalidArgumentException('Aggregation functions must be callables.');
            }
        }
        foreach ($defaultActivationFunctions as $defaultActivationFunction) {
            if (!is_callable($defaultActivationFunction)) {
                throw new InvalidArgumentException('Default activation functions must be callables.');
            }
        }
        foreach ($defaultAggregationFunctions as $defaultAggregationFunction) {
            if (!is_callable($defaultAggregationFunction)) {
                throw new InvalidArgumentException('Default aggregation functions must be callables.');
            }
        }

        $this->nbInputs = $nbInputs;
        $this->nbOutputs = $nbOutputs;
        $this->populationSize = $populationSize;
        $this->genotypeFactory = $genotypeFactory;
        $this->agentFactory = $agentFactory;

        $initializationMethod(
            $this,
            $defaultActivationFunctions,
            $defaultAggregationFunctions
        );
    }
}
