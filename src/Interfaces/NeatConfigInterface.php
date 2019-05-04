<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Interfaces;

interface NeatConfigInterface
{
    // NEAT algorithm

    public function nbInputs(int $nbInputs): NeatConfigInterface;
    public function getNbInputs(): int;

    public function nbOutputs(int $nbOutputs): NeatConfigInterface;
    public function getNbOutputs(): int;

    public function populationSize(int $populationSize): NeatConfigInterface;
    public function getPopulationSize(): int;

    public function maxGenerations(int $maxGenerations): NeatConfigInterface;
    public function getMaxGenerations(): int;

    public function fitnessCriterion(callable $criterion): NeatConfigInterface;
    public function getFitnessCriterion(): callable;

    public function fitnessThreshold(float $threshold): NeatConfigInterface;
    public function getFitnessThreshold(): float;

    public function fitnessFunction(callable $fitnessFn): NeatConfigInterface;
    public function getFitnessFunction(): ?callable;

    // Initialization

    public function initializationMethod(callable $method): NeatConfigInterface;
    public function getInitializationMethod(): callable;

    public function activationFunctions(array $activationFns): NeatConfigInterface;
    public function getActivationFunctions(): array;

    public function defaultActivationFunction(int $activationFnIndex): NeatConfigInterface;
    public function getDefaultActivationFunction(): int;

    public function aggregationFunctions(array $aggregationFns): NeatConfigInterface;
    public function getAggregationFunctions(): array;

    public function defaultAggregationFunction(int $aggregationFnIndex): NeatConfigInterface;
    public function getDefaultAggregationFunction(): int;

    public function weightInitializationMean(float $mean): NeatConfigInterface;
    public function getWeightInitializationMean(): float;

    public function weightInitializationStdev(float $stdev): NeatConfigInterface;
    public function getWeightInitializationStdev(): float;

    // Weights

    public function weightMinValue(float $val): NeatConfigInterface;
    public function getWeightMinValue(): float;

    public function weightMaxValue(float $val): NeatConfigInterface;
    public function getWeightMaxValue(): float;

    // Mutation rates

    /**
     * @todo
        'mut_interagent_rate' => 'default',

        'mut_activation_fn_rate' => 'default',
        'mut_aggregation_fn_rate' => 'default',
        'mut_add_node' => 'default',
        'mut_add_conn' => 'default',
        'mut_remove_conn' => 'default',
        'mut_change_weight' => 'default',
        'mut_reverse_weight' => 'default',
        'mut_replace_weight' => 'default',
        'mut_activate_node' => 'default',
        'mut_deactivate_node' => 'default',
     */

    // Neat classes

    public function genomeClass(string $className): NeatConfigInterface;
    public function getGenomeClass(): string;

    public function genomePoolClass(string $className): NeatConfigInterface;
    public function getGenomePoolClass(): string;

    public function genePoolClass(string $className): NeatConfigInterface;
    public function getGenePoolClass(): string;

    // Validation

    public function validatePoolCreation(): void;
    public function validateConfig(): void;
}
