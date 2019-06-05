<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Algo;

use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Exceptions\InvalidArgumentException;
use IngeniozIT\Neat\Genotype\Interfaces\GenotypeFactoryInterface;
use IngeniozIT\Neat\Agents\Interfaces\AgentFactoryInterface;
use IngeniozIT\Neat\Agents\Interfaces\AgentInterface;
use IngeniozIT\Neat\Threshold\Interfaces\ThresholdInterface;

class Pool implements PoolInterface
{
    use PoolTrait;

    protected $nbInputs;
    protected $nbOutputs;
    protected $populationSize;
    protected $genotypeFactory;
    protected $agentFactory;

    protected $activationFunctions;
    protected $aggregationFunctions;

    protected $sensorGenes = [];
    protected $outputGenes = [];

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
        $this->activationFunctions = $activationFunctions;
        $this->aggregationFunctions = $aggregationFunctions;

        for ($i = 1; $i <= $this->nbInputs; ++$i) {
            $this->sensorGenes[$i] = $this->genotypeFactory->createSensorNodeGenotype(null);
        }

        for ($i = 1; $i <= $this->nbOutputs; ++$i) {
            $this->outputGenes[$this->nbInputs + $i] = $this->genotypeFactory->createOutputNodeGenotype(null);
        }

        $initializationMethod(
            $this,
            $defaultActivationFunctions,
            $defaultAggregationFunctions
        );
    }

    public function nbInputs(): int
    {
        return $this->nbInputs;
    }

    public function nbOutputs(): int
    {
        return $this->nbOutputs;
    }

    public function sensorGenes(): array
    {
        return $this->sensorGenes;
    }

    public function outputGenes(): array
    {
        return $this->outputGenes;
    }

    public function genotypeFactory(): GenotypeFactoryInterface
    {
        return $this->genotypeFactory;
    }

    public function agentFactory(): AgentFactoryInterface
    {
        return $this->agentFactory;
    }

    public function populationSize(): int
    {
        return \is_int($this->populationSize) ? $this->populationSize : $this->populationSize();
    }

    public function maxNodeInnovation(): int
    {
        $max = 0;
        foreach ($this->agents as $agent) {
            $max = max($max, $agent->maxNodeInnovation());
        }
        return $max;
    }

    public function maxConnectInnovation(): int
    {
        $max = 0;
        foreach ($this->agents as $agent) {
            $max = max($max, $agent->maxConnectInnovation());
        }
        return $max;
    }

    public function vectors(): array
    {
        $maxNodeInnovNb = $this->maxNodeInnovation();
        $maxConnectInnovNb = $this->maxConnectInnovation();

        $vectors = [];
        foreach ($this->agents as $id => $agent) {
            $vectors[$id] = $agent->toVector(
                $maxNodeInnovNb,
                $maxConnectInnovNb,
                $this->activationFunctions,
                $this->aggregationFunctions
            );
        }
        return $vectors;
    }

    public function addAgent(AgentInterface $agent): void
    {
        $this->agents[] = $agent;
    }

    public function agentNb(int $agentId): AgentInterface
    {
        if (!isset($this->agents[$agentId])) {
            throw new RuntimeException("Agent $agentId does not exist.");
        }
        return $this->agents[$agentId];
    }

    public function removeAgent(int $agentId): void
    {
        if (!isset($this->agents[$agentId])) {
            throw new RuntimeException("Agent $agentId does not exist.");
        }
        unset($this->agents[$agentId]);
    }

    public function assignSpecies(int $speciesId, array $agentsIds): void
    {
        foreach ($agentsIds as $agentId) {
            $this->agents[$agentId]->setSpecies($speciesId);
        }
    }

    public function sort(ThresholdInterface $threshold): void
    {
        usort($this->agents, [$threshold, 'sort']);
    }

    public function champion(): AgentInterface
    {
        foreach ($this->agents as $agent) {
            return $agent;
        }
    }
}
