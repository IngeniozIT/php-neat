<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

use IngeniozIT\NEAT\NeatConfig;
use IngeniozIT\NEAT\Interfaces\NeatInterface;

use IngeniozIT\NEAT\Interfaces\GenePoolInterface;
use IngeniozIT\NEAT\Interfaces\GenomePoolInterface;
use IngeniozIT\NEAT\Interfaces\NeatConfigInterface;

use IngeniozIT\NEAT\Exceptions\NeatException;

use IngeniozIT\Math\ActivationFunction;
use IngeniozIT\Math\Random;
use IngeniozIT\Math\KMeans;

class NEAT extends NeatConfig implements NeatInterface
{
    // Current generation

    protected $currentGeneration = 0;

    public function currentGeneration(): int
    {
        return $this->currentGeneration;
    }

    // Initialization

    protected $genomePool = null;

    public function setPool(GenomePoolInterface &$pool): NeatInterface
    {
        $this->genomePool = $pool;

        return $this;
    }

    public function &pool(): GenomePoolInterface
    {
        if (null === $this->genomePool) {
            $this->createPool();
        }

        return $this->genomePool;
    }

    public function createPool(): NeatInterface
    {
        $this->validatePoolCreation();

        $genePoolClass = $this->getGenePoolClass();
        $genomePoolClass = $this->getGenomePoolClass();

        $genePool = new $genePoolClass();
        $genomePool = new $genomePoolClass($genePool);

        $this->setPool($genomePool);
        $method = $this->getInitializationMethod();
        $method($this);

        return $this;
    }

    // run algorithm

    public function run(): bool
    {
        $generation = ($this->maxGenerations ?? -2) + 1;

        while (--$generation) {
            if ($this->runOnce()) {
                return true;
            }
        }

        return false;
    }

    public function runOnce(): bool
    {
        $this->pool();

        ++$this->currentGeneration;

        $this->speciation();
        $this->evaluation();
        $this->mating();

        return $this->thresholdMet();
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
        uasort(
            $genomesFitnesses,
            function (array $a, array $b): int {
                return $this->fitnessCriterion === 'min' ?
                $b[1] <=> $a[1] :
                $a[1] <=> $b[1];
            }
        );

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

    // Implementation

    public static function min(GenomePoolInterface $genomePool, float $threshold): bool
    {
        foreach ($genomePool->getGenomes() as $genome) {
            if ($genome->getFitness() <= $threshold) {
                return true;
            }
        }

        return false;
    }

    public static function max(GenomePoolInterface $genomePool, float $threshold): bool
    {
        foreach ($genomePool->getGenomes() as $genome) {
            if ($genome->getFitness() >= $threshold) {
                return true;
            }
        }

        return false;
    }

    public static function initPartiallyConnected(NEAT &$neat)
    {
        self::initNotConnected($neat);

        $genomePool = $neat->pool();
        $genePool = $genomePool->genePool();
        $genomes = $genomePool->genomes();

        $inputGenes = $genePool->inputGenes();
        $outputGenes = $genePool->outputGenes();

        foreach ($genomes as &$genome) {
            $inId = $inputGenes[rand(0, count($inputGenes) - 1)];
            $outId = $outputGenes[rand(0, count($outputGenes) - 1)];
            $genome->addConnexion(
                $genePool->connexionGeneId($inId, $outId),
                $inId,
                $outId,
                Random::nrand(
                    $neat->getWeightInitializationMean(),
                    $neat->getWeightInitializationStdev()
                )
            );
        }
    }

    public static function initFullyConnected(NEAT &$neat)
    {
        self::initNotConnected($neat);

        $genomePool = $neat->pool();
        $genePool = $genomePool->genePool();
        $genomes = $genomePool->genomes();

        $inputGenes = $genePool->inputGenes();
        $outputGenes = $genePool->outputGenes();

        foreach ($inputGenes as $inId) {
            foreach ($outputGenes as $outId) {
                $connId = $genePool->connexionGeneId($inId, $outId);
                foreach ($genomes as &$genome) {
                    $genome->addConnexion(
                        $connId,
                        $inId,
                        $outId,
                        Random::nrand(
                            $neat->getWeightInitializationMean(),
                            $neat->getWeightInitializationStdev()
                        )
                    );
                }
            }
        }
    }

    public static function initNotConnected(NEAT &$neat)
    {
        $neat->validatePoolCreation();

        $genomePool = $neat->pool();
        $genePool = $genomePool->genePool();

        $genomeClass = $neat->getGenomeClass();

        // Gene pool
        $nbInputs = $neat->getNbInputs();
        for ($i = 1; $i <= $nbInputs; ++$i) {
            $genePool->addInputGene();
        }
        $nbOutputs = $neat->getNbOutputs();
        for ($i = 1; $i <= $nbOutputs; ++$i) {
            $genePool->addOutputGene();
        }

        // Genome pool
        $inputGenes = $genePool->inputGenes();
        $outputGenes = $genePool->outputGenes();
        $populationSize = $neat->getPopulationSize();
        for ($i = 1; $i <= $populationSize; ++$i) {
            $genome = new $genomeClass($neat->getActivationFunctions(), $neat->getAggregationFunctions());

            foreach ($inputGenes as $inId) {
                $genome->addinputNode($inId, 0, 0);
            }

            foreach ($outputGenes as $outId) {
                $genome->addOutputNode($outId, 0, 0);
            }

            $genomePool->addGenome($genome);
        }
    }
}
