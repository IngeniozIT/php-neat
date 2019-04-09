<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

class NEAT
{
	// nb inputs

	protected $nbInputs = null;

	public function nbInputs(int $nbInputs): NEAT
	{
		$this->nbInputs = $nbInputs;

		if (null !== $this->getNbOutputs() && null === $this->getPopulationSize()) {
			$this->populationSize(2 * $this->getNbInputs() * $this->getNbOutputs());
		}

		return $this;
	}

	public function getNbInputs(): ?int
	{
		return $this->nbInputs;
	}

	// nb outputs

	protected $nbOutputs = null;

	public function nbOutputs(int $nbOutputs): NEAT
	{
		$this->nbOutputs = $nbOutputs;

		if (null !== $this->getNbInputs() && null === $this->getPopulationSize()) {
			$this->populationSize(2 * $this->getNbInputs() * $this->getNbOutputs());
		}

		return $this;
	}

	public function getNbOutputs(): ?int
	{
		return $this->nbOutputs;
	}

	// population size

	protected $populationSize = null;

	public function populationSize(int $populationSize): NEAT
	{
		$this->populationSize = $populationSize;
		return $this;
	}

	public function getPopulationSize(): ?int
	{
		return $this->populationSize;
	}

	// current generation

	protected $currentGeneration = 0;

	public function currentGeneration(): int
	{
		return $this->currentGeneration;
	}

	// maximum generations

	protected $maxGenerations = null;

	public function maxGenerations(?int $maxGenerations): NEAT
	{
		$this->maxGenerations = $maxGenerations;
		return $this;
	}

	public function getMaxGenerations(): ?int
	{
		return $this->maxGenerations;
	}

	// fitness

	protected $fitnessCriterion = null;
	protected $fitnessThreshold = null;

	public function fitnessThreshold(?string $criterion, float $threshold = null): NEAT
	{
		if (
			null !== $criterion &&
			'min' !== $criterion &&
			'max' !== $criterion
		) {
			throw new \Exception('Invalid fitness criterion "'.$criterion.'" (must be "min" or "max")');
		}

		$this->fitnessCriterion = $criterion;
		$this->fitnessThreshold = $threshold;

		return $this;
	}

	public function getFitnessThreshold(): array
	{
		return [$this->fitnessCriterion, $this->fitnessThreshold];
	}

	// fitness function

	protected $fitnessFunction = null;

	public function fitnessFunction(?callable $fitFunc): NEAT
	{
		$this->fitnessFunction = $fitFunc;
		return $this;
	}

	public function getFitnessFunction(): ?callable
	{
		return $this->fitnessFunction;
	}

	// initialization method

	protected $fullyConnected = true;

	public function fullyConnected(bool $fullyConnected): NEAT
	{
		$this->fullyConnected = $fullyConnected;
		return $this;
	}

	public function getFullyConnected(): bool
	{
		return $this->fullyConnected;
	}

	// initialization

	protected $genomePool = null;
	protected $genePool = null;

	public function createGenomePool(): NEAT
	{
		/**
		 * @todo
		 */

		return $this;
	}

	public function importGenomePool(): NEAT
	{
		/**
		 * @todo
		 */

		return $this;
	}

	public function getGenomePool(): ?GenomePool
	{
		return $this->genomePool;
	}

	public function createGenePool(): NEAT
	{
		/**
		 * @todo
		 */

		return $this;
	}

	public function importGenePool(): NEAT
	{
		/**
		 * @todo
		 */

		return $this;
	}

	public function getGenePool(): ?GenePool
	{
		return $this->genePool;
	}

	// run algorithm

	public function run()
	{
		$generation = ($this->maxGenerations ?? -2) + 1;

		while (
			--$generation !== 0 &&
			$this->runOnce()
		);
	}

	public function runOnce(): bool
	{
		$this->prepareRun();

		++$this->currentGeneration;

		$this->speciation();
		$this->evaluation();

		if ($this->thresholdMet()) {
			return false;
		}

		$this->mating();

		return true;
	}

	protected function prepareRun(): void
	{
		if (null === $this->genePool) {
			$this->createGenePool();
		}

		if (null === $this->genomePool) {
			$this->createGenomePool();
		}
	}

	protected function speciation()
	{
		/**
		 * @todo
		 */
	}

	protected function evaluation()
	{
		/**
		 * @todo
		 */
	}

	protected function mating()
	{
		/**
		 * @todo
		 */
	}

	protected function thresholdMet(): bool
	{
		$thresholdMet = false;

		/**
		 * @todo
		 */

		return $thresholdMet;
	}
}
