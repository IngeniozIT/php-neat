<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Algo;

use IngeniozIT\Neat\Algo\Interfaces\NeatFactoryInterface;
use IngeniozIT\Neat\Implementation\Initialization\FullyConnectedInitialization;
use IngeniozIT\Neat\Implementation\Mating\OriginalMating;
use IngeniozIT\Neat\Implementation\Speciation\KmeansSpeciation;
use IngeniozIT\Neat\Implementation\Interfaces\SpeciationInterface;
use IngeniozIT\Neat\Implementation\Interfaces\MatingInterface;
use IngeniozIT\Neat\Threshold\Interfaces\ThresholdInterface;
use IngeniozIT\Neat\Genotype\GenotypeFactory;
use IngeniozIT\Neat\Agents\AgentFactory;
use IngeniozIT\Neat\Algo\Pool;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Math\ActivationFunction;

class NeatFactory implements NeatFactoryInterface
{
    protected $initializationMethod;
    protected $matingFunction;
    protected $speciationFunction;
    protected $activationFunctions = [[ActivationFunction::class, 'sigmoid']];
    protected $aggregationFunctions = ['array_sum'];
    protected $defaultActivationFunctions = [[ActivationFunction::class, 'sigmoid']];
    protected $defaultAggregationFunctions = ['array_sum'];

    protected $genotypeFactory;
    protected $agentFactory;

    public function __construct()
    {
        $this->initializationMethod = new FullyConnectedInitialization();
        $this->matingFunction = new OriginalMating();
        $this->speciationFunction = new KmeansSpeciation();
        $this->genotypeFactory = new GenotypeFactory();
        $this->agentFactory = new AgentFactory();
    }

    public function setSpeciationFunction(SpeciationInterface $speciationFunction): void
    {
        $this->speciationFunction = $speciationFunction;
    }

    public function setMatingFunction(MatingInterface $matingFunction): void
    {
        $this->matingFunction = $matingFunction;
    }

    public function setActivationFunctions(array $activationFunctions): void
    {
        $this->activationFunctions = $activationFunctions;
    }

    public function setDefaultActivationFunction(array $activationFunctions): void
    {
        $this->defaultActivationFunctions = $activationFunctions;
    }

    public function createPool(int $nbInputs, int $nbOutputs, $populationSize): PoolInterface
    {
        return new Pool($nbInputs, $nbOutputs, $populationSize, $this->initializationMethod, $this->activationFunctions, $this->aggregationFunctions, $this->defaultActivationFunctions, $this->defaultAggregationFunctions, $this->genotypeFactory, $this->agentFactory);
    }

    public function createNeat(int $nbInputs, int $nbOutputs, $populationSize, ThresholdInterface $threshold, callable $fitnessFunction): Neat
    {
        $pool = $this->createPool($nbInputs, $nbOutputs, $populationSize);
        $neat = new Neat($pool, $threshold, $fitnessFunction, $this->matingFunction, $this->speciationFunction);

        return $neat;
    }
}
