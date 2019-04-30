<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

use IngeniozIT\NEAT\Interfaces\GenePoolInterface;
use IngeniozIT\NEAT\Interfaces\GenomePoolInterface;
use IngeniozIT\NEAT\Exceptions\NeatException;
use IngeniozIT\Math\ActivationFunction;
use IngeniozIT\Math\Random;
use IngeniozIT\Math\KMeans;

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
        if (null !== $criterion &&
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
    protected $genomePoolClass = GenomePool::class;
    protected $genePool = null;
    protected $genePoolClass = GenePool::class;
    protected $genomeClass = Genome::class;

    public function createGenomePool(): NEAT
    {
        $this->genomePool = new $this->genomePoolClass();

        $genePool = $this->getGenePool();
        if (null === $genePool) {
            $this->createGenePool();
            $genePool = $this->getGenePool();
        }

        $inputGenes = $genePool->getInputGenes();
        $outputGenes = $genePool->getOutputGenes();

        $populationSize = $this->getPopulationSize();

        for ($i = 1; $i <= $populationSize; ++$i) {
            $genome = new $this->genomeClass([[ActivationFunction::class, 'sigmoid']], ['array_sum']);

            foreach ($inputGenes as $inId) {
                $genome->addinputNode($inId, 0, 0);
            }

            foreach ($outputGenes as $outId) {
                $genome->addOutputNode($outId, 0, 0);
            }

            if ($this->getFullyConnected()) {
                foreach ($inputGenes as $inId) {
                    foreach ($outputGenes as $outId) {
                        $genome->addConnexion(
                            $genePool->getConnexionGeneId($inId, $outId),
                            $inId,
                            $outId,
                            Random::nrand(0, 1)
                        );
                    }
                }
            }

            $this->genomePool->addGenome($genome);
        }

        return $this;
    }

    public function importGenomePool(GenomePoolInterface &$genomePool): NEAT
    {
        $this->genomePool = $genomePool;

        return $this;
    }

    public function &getGenomePool(): ?GenomePoolInterface
    {
        return $this->genomePool;
    }

    public function createGenePool(): NEAT
    {
        $this->genePool = new $this->genePoolClass();

        $nbInputs = $this->getNbInputs();
        if (null === $nbInputs) {
            throw new NeatException('No number of inputs specified.');
        }

        $nbOutputs = $this->getNbOutputs();
        if (null === $nbOutputs) {
            throw new NeatException('No number of outputs specified.');
        }

        for ($i = 1; $i <= $nbInputs; ++$i) {
            $this->genePool->addInputGene();
        }

        for ($i = 1; $i <= $nbOutputs; ++$i) {
            $this->genePool->addOutputGene();
        }

        if ($this->getFullyConnected()) {
            $inputGenes = $this->genePool->getInputGenes();
            $outputGenes = $this->genePool->getOutputGenes();

            foreach ($inputGenes as $inId) {
                foreach ($outputGenes as $outId) {
                    $this->genePool->addConnexionGene($inId, $outId);
                }
            }
        }

        return $this;
    }

    public function importGenePool(GenePoolInterface &$genePool): NEAT
    {
        $this->genePool = $genePool;

        return $this;
    }

    public function &getGenePool(): ?GenePoolInterface
    {
        return $this->genePool;
    }

    // run algorithm

    public function run()
    {
        $generation = ($this->maxGenerations ?? -2) + 1;

        while (--$generation &&
            $this->runOnce()
        ) {
        }
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

    protected $speciated = false;

    protected function speciation()
    {
        if ($this->speciated) {
            return;
        }

        $genomePool = $this->getGenomePool();
        $genomePool->resetSpecies();
        $genomes = $genomePool->getVectors();
        $kMeans = new KMeans($genomes);
        while (!$kMeans->classifyAndOptimize()) {
        }
        $species = $kMeans->clusters();

        foreach ($species as $speciesId => $gens) {
            $genomePool->assignGenomesToSpecies($gens, $speciesId);
        }

        $this->speciated = true;
    }

    protected function evaluation()
    {
        $genomes = $this->getGenomePool()->getGenomes();
        foreach ($genomes as $genome) {
            $genome->setFitness(null);
        }
        $this->getFitnessFunction()($genomes);
        foreach ($genomes as $genome) {
            if (null === $genome->getFitness()) {
                throw new NeatException('All genomes must have a fitness.');
            }
        }
    }

    protected function mating()
    {
        $this->removeWorstGenomes();
        $this->mateGenomes();
        $this->speciated = false;
        $this->speciation();
    }

    protected function removeWorstGenomes()
    {
        $genomePool = $this->getGenomePool();
        $genomes = $genomePool->getGenomes();

        // Get genoems fitnesses
        $genomesFitnesses = [];
        foreach ($genomes as $genomeId => $genome) {
            $genomesFitnesses[] = [
                $genomeId,
                $genome->getFitness()
            ];
        }
        uasort($genomesFitnesses, function (array $a, array $b): int {
            return $this->fitnessCriterion === 'min' ?
                $b[1] <=> $a[1] :
                $a[1] <=> $b[1];
        });

        // Count how many genomes will be killed
        /**
         * @todo Change this number into a parameter (callable ?)
         */
        $genomesToKill = floor(count($genomesFitnesses) ** 0.5);

        // Kill genomes
        for ($genomesToKill = $genomesToKill; $genomesToKill; --$genomesToKill) {
            $genomePool->removeGenome(array_shift($genomesFitnesses)[0]);
        }
    }

    protected function mateGenomes()
    {
        $targetPops = $this->getSpeciesTargetPopulations();

        /**
         * @todo mate genomes inside their species until the target population is reached
         * - Until target pop is reached
         *   - Select 2 genomes from the species (random ? semi-random ?)
         *   - Create an offspring based on the 2 genomes
         *   - Mutate the offspring
         */
    }

    protected function getSpeciesTargetPopulations(): array
    {
        $targetPops = [];

        /**
         * @todo calculate the target population of every species there is
         * @todo Change the base formula into a parameter (callable)
         */

        return $targetPops;
    }

    protected function thresholdMet(): bool
    {
        $thresholdMet = false;

        $genomes = $this->getGenomePool()->getGenomes();
        if ($this->fitnessCriterion === 'min') {
            foreach ($genomes as $genome) {
                if ($genome->getFitness() <= $this->fitnessThreshold) {
                    return true;
                }
            }
        } elseif ($this->fitnessCriterion === 'max') {
            foreach ($genomes as $genome) {
                if ($genome->getFitness() >= $this->fitnessThreshold) {
                    return true;
                }
            }
        }

        return false;
    }
}
