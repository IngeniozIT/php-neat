<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

use IngeniozIT\NEAT\Interfaces\NeatConfigInterface;
use IngeniozIT\NEAT\Interfaces\GenomePoolInterface;
use IngeniozIT\Math\ActivationFunction;
use IngeniozIT\NEAT\Exceptions\NeatConfigException;
use IngeniozIT\NEAT\Genome;
use IngeniozIT\NEAT\GenomePool;
use IngeniozIT\NEAT\GenePool;
use IngeniozIT\NEAT\NeatUtils;

abstract class NeatConfig implements NeatConfigInterface
{
    protected $config = [
        'nb_inputs' => 0,
        'nb_outputs' => 0,
        'population_size' => 0,
        'max_generations' => 0,
        'fitness_criterion' => [NeatUtils::class, 'min'],
        'fitness_threshold' => 0.0,
        'fitness_function' => null,

        'initialization_method' => [NeatUtils::class, 'initFullyConnected'],
        'activation_functions' => [
            0 => [ActivationFunction::class, 'sigmoid'],
        ],
        'default_activation_function' => 0,
        'aggregation_functions' => [
            0 => 'array_sum',
        ],
        'default_aggregation_function' => 0,
        'weight_initialization_mean' => 0.0,
        'weight_initialization_stdev' => 1.0,

        'weight_min_value' => -10.0,
        'weight_max_value' => 10.0,

        'genome_class' => Genome::class,
        'genome_pool_class' => GenomePool::class,
        'gene_pool_class' => GenePool::class,
    ];

    // NEAT algorithm

    public function nbInputs(int $nbInputs): NeatConfigInterface
    {
        if ($nbInputs <= 0) {
            throw new NeatConfigException('Number of inputs must be positive.');
        }

        if (0 !== $this->getNbOutputs() && 0 === $this->getPopulationSize()) {
            $this->populationSize(2 * $nbInputs * $this->getNbOutputs());
        }

        $this->config['nb_inputs'] = $nbInputs;

        return $this;
    }

    public function getNbInputs(): int
    {
        return $this->config['nb_inputs'];
    }

    public function nbOutputs(int $nbOutputs): NeatConfigInterface
    {
        if ($nbOutputs <= 0) {
            throw new NeatConfigException('Number of outputs must be positive.');
        }

        if (0 !== $this->getNbInputs() && 0 === $this->getPopulationSize()) {
            $this->populationSize(2 * $this->getNbInputs() * $nbOutputs);
        }

        $this->config['nb_outputs'] = $nbOutputs;

        return $this;
    }

    public function getNbOutputs(): int
    {
        return $this->config['nb_outputs'];
    }

    public function populationSize(int $populationSize): NeatConfigInterface
    {
        if ($populationSize <= 0) {
            throw new NeatConfigException('Population size must be positive.');
        }

        $this->config['population_size'] = $populationSize;

        return $this;
    }

    public function getPopulationSize(): int
    {
        return $this->config['population_size'];
    }

    public function maxGenerations(int $maxGenerations): NeatConfigInterface
    {
        if ($maxGenerations < 0) {
            throw new NeatConfigException('Maximum generations must be positive or 0.');
        }

        $this->config['max_generations'] = $maxGenerations;

        return $this;
    }

    public function getMaxGenerations(): int
    {
        return $this->config['max_generations'];
    }

    public function fitnessCriterion(callable $criterion): NeatConfigInterface
    {
        /**
         * @todo check if callable has right parameters
         */
        $this->config['fitness_criterion'] = $criterion;

        return $this;
    }

    public function getFitnessCriterion(): callable
    {
        return $this->config['fitness_criterion'];
    }

    public function fitnessThreshold(float $threshold): NeatConfigInterface
    {
        $this->config['fitness_threshold'] = $threshold;

        return $this;
    }

    public function getFitnessThreshold(): float
    {
        return $this->config['fitness_threshold'];
    }

    public function fitnessFunction(callable $fitnessFn): NeatConfigInterface
    {
        /**
         * @todo check if callable has right parameters
         */
        $this->config['fitness_function'] = $fitnessFn;

        return $this;
    }

    public function getFitnessFunction(): ?callable
    {
        return $this->config['fitness_function'];
    }

    // Initialization

    public function initializationMethod(callable $method): NeatConfigInterface
    {
        /**
         * @todo check if callable has right parameters
         */
        $this->config['initialization_method'] = $method;

        return $this;
    }

    public function getInitializationMethod(): callable
    {
        return $this->config['initialization_method'];
    }

    public function activationFunctions(array $activationFns): NeatConfigInterface
    {
        /**
         * @todo check if callable has right parameters
         */
        $this->config['activation_functions'] = $activationFns;

        return $this;
    }

    public function getActivationFunctions(): array
    {
        return $this->config['activation_functions'];
    }

    public function defaultActivationFunction(int $activationFnIndex): NeatConfigInterface
    {
        /**
         * @todo check if callable has right parameters
         */
        $this->config['default_activation_function'] = $activationFnIndex;

        return $this;
    }

    public function getDefaultActivationFunction(): int
    {
        return $this->config['default_activation_function'];
    }

    public function aggregationFunctions(array $aggregationFns): NeatConfigInterface
    {
        /**
         * @todo check if callable has right parameters
         */
        $this->config['aggregation_functions'] = $aggregationFns;

        return $this;
    }

    public function getAggregationFunctions(): array
    {
        return $this->config['aggregation_functions'];
    }

    public function defaultAggregationFunction(int $aggregationFnIndex): NeatConfigInterface
    {
        /**
         * @todo check if callable has right parameters
         */
        $this->config['default_aggregation_function'] = $aggregationFnIndex;

        return $this;
    }

    public function getDefaultAggregationFunction(): int
    {
        return $this->config['default_aggregation_function'];
    }

    public function weightInitializationMean(float $mean): NeatConfigInterface
    {
        $this->config['weight_initialization_mean'] = $mean;

        return $this;
    }

    public function getWeightInitializationMean(): float
    {
        return $this->config['weight_initialization_mean'];
    }

    public function weightInitializationStdev(float $stdev): NeatConfigInterface
    {
        $this->config['weight_initialization_stdev'] = $stdev;

        return $this;
    }

    public function getWeightInitializationStdev(): float
    {
        return $this->config['weight_initialization_stdev'];
    }

    // Weights

    public function weightMinValue(float $val): NeatConfigInterface
    {
        if ($val > $this->getWeightMaxValue()) {
            throw new NeatConfigException('Weight min value must be <= weight max value.');
        }

        $this->config['weight_min_value'] = $val;

        return $this;
    }

    public function getWeightMinValue(): float
    {
        return $this->config['weight_min_value'];
    }

    public function weightMaxValue(float $val): NeatConfigInterface
    {
        if ($val < $this->getWeightMinValue()) {
            throw new NeatConfigException('Weight max value must be >= weight min value.');
        }

        $this->config['weight_max_value'] = $val;

        return $this;
    }

    public function getWeightMaxValue(): float
    {
        return $this->config['weight_max_value'];
    }

    // Mutation rates

    // Neat classes

    public function genomeClass(string $className): NeatConfigInterface
    {
        if (!class_exists($className)) {
            throw new NeatConfigException('Genome class "'.$className.'" does not exist.');
        }

        $this->config['genome_class'] = $className;

        return $this;
    }

    public function getGenomeClass(): string
    {
        return $this->config['genome_class'];
    }

    public function genomePoolClass(string $className): NeatConfigInterface
    {
        if (!class_exists($className)) {
            throw new NeatConfigException('Genome pool class "'.$className.'" does not exist.');
        }

        $this->config['genome_pool_class'] = $className;

        return $this;
    }

    public function getGenomePoolClass(): string
    {
        return $this->config['genome_pool_class'];
    }

    public function genePoolClass(string $className): NeatConfigInterface
    {
        if (!class_exists($className)) {
            throw new NeatConfigException('Gene pool class "'.$className.'" does not exist.');
        }

        $this->config['gene_pool_class'] = $className;

        return $this;
    }

    public function getGenePoolClass(): string
    {
        return $this->config['gene_pool_class'];
    }

    // Validation

    public function validatePoolCreation(): void
    {
        $this
            ->nbInputs($this->getNbInputs())
            ->nbOutputs($this->getNbOutputs())
            ->populationSize($this->getPopulationSize());
    }

    public function validateConfig(): void
    {
        $this
            ->nbInputs($this->getNbInputs())
            ->nbOutputs($this->getNbOutputs())
            ->populationSize($this->getPopulationSize())
            ->maxGenerations($this->getMaxGenerations())
            ->fitnessCriterion($this->getFitnessCriterion())
            ->fitnessThreshold($this->getFitnessThreshold())
            ->fitnessFunction($this->getFitnessFunction())
            ->initializationMethod($this->getInitializationMethod())
            ->activationFunctions($this->getActivationFunctions())
            ->defaultActivationFunction($this->getDefaultActivationFunction())
            ->aggregationFunctions($this->getAggregationFunctions())
            ->defaultAggregationFunction($this->getDefaultAggregationFunction())
            ->weightInitializationMean($this->getWeightInitializationMean())
            ->weightInitializationStdev($this->getWeightInitializationStdev())
            ->weightMinValue($this->getWeightMinValue())
            ->weightMaxValue($this->getWeightMaxValue());
    }
}
