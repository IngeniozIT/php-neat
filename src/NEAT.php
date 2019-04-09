<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

class NEAT
{
	const FITNESS_MIN = 'min';
	const FITNESS_MAX = 'max';

	protected $config = [
		'nb_inputs' => null,
		'nb_outputs' => null,
		'pop_size' => null,
		'max_generations' => null,
		'fitness_criterion' => null,
		'fitness_threshold' => null,
		'fitness_function' => null,
	];

	public function genomePool(int $nbInputs, int $nbOutputs, int $popSize): void
	{
		$this->config['nb_inputs'] = $nbInputs;
		$this->config['nb_outputs'] = $nbOutputs;
		$this->config['pop_size'] = $popSize;

		/**
		 * @todo create genome pool
		 */
	}

	public function maxGenerations(int $nb): void
	{
		$this->config['max_generations'] = $nb;
	}

	public function fitnessThreshold(string $criterion, float $threshold): void
	{
		$this->config['fitness_criterion'] = $criterion;
		$this->config['fitness_threshold'] = $threshold;
	}

	public function fitnessFunction(callable $fitFunc): void
	{
		$this->config['fitness_function'] = $fitFunc;
	}

	public function run()
	{
		/**
		 * @todo speciation, evaluation, mating
		 */
	}
}
