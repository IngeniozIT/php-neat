<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\NEAT;
use IngeniozIT\NEAT\NeatUtils;
use IngeniozIT\NEAT\Interfaces\GenomePoolInterface;
use IngeniozIT\NEAT\GenomePool;
use IngeniozIT\NEAT\GenePool;

/**
 * @coversDefaultClass \IngeniozIT\NEAT\NEAT
 */
class NeatTest extends TestCase
{
    protected $className = NEAT::class;

    public function testCreatePoolNoInputsNumber()
    {
        $neat = new $this->className();

        $neat
            ->nbOutputs(2)
            ->populationSize(10);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->createPool();
    }

    public function testCreatePoolNoOutputsNumber()
    {
        $neat = new $this->className();

        $neat
            ->nbInputs(3)
            ->populationSize(10);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->createPool();
    }

    public function testSetPool()
    {
        $neat = new $this->className();

        $genomePool = new GenomePool(new GenePool());

        $neat
            ->nbInputs(3)
            ->populationSize(10)
            ->setPool($genomePool);

        $this->assertSame($genomePool, $neat->pool());
    }

    public function testCreatePool()
    {
        $neat = new $this->className();

        $neat
            ->nbInputs(3)
            ->nbOutputs(2)
            ->populationSize(10)
            ->createPool();

        $this->assertInstanceOf(GenomePoolInterface::class, $neat->pool());
    }

    public function testPool()
    {
        $neat = new $this->className();

        $neat
            ->nbInputs(3)
            ->nbOutputs(2)
            ->populationSize(10);

        $this->assertInstanceOf(GenomePoolInterface::class, $neat->pool());
    }

    public function testSpeciation()
    {
        $neat = new $this->className();

        $neat
            ->nbInputs(3)
            ->nbOutputs(2)
            ->populationSize(3)
            ->initializationMethod([NeatUtils::class, 'initFullyConnected']);

        $pool = $neat->pool();
        $genomes = $pool->genomes();
        foreach ($genomes as $genome) {
            $this->assertNull($genome->species());
        }

        $neat->speciation();

        foreach ($genomes as $genome) {
            $this->assertNotNull($genome->species());
        }

        $species = $pool->species();
        $neat->speciation();
        $this->assertSame($species, $pool->species());
    }

    public function testEvaluation()
    {
        $neat = new $this->className();

        $neat
            ->nbInputs(3)
            ->nbOutputs(2)
            ->populationSize(3)
            ->initializationMethod([NeatUtils::class, 'initFullyConnected'])
            ->fitnessFunction([$this, 'xorFitnessFunction']);

        $pool = $neat->pool();
        $genomes = $pool->genomes();

        foreach ($genomes as $genome) {
            $this->assertNull($genome->fitness());
        }

        $neat->evaluation();

        $fitnesses = [];
        foreach ($genomes as $genomeId => $genome) {
            $fitness = $genome->fitness();
            $this->assertNotNull($fitness);
            $fitnesses[$genomeId] = $fitness;
        }

        $neat->evaluation();

        $fitnessesCmp = [];
        foreach ($genomes as $genomeId => $genome) {
            $fitness = $genome->fitness();
            $fitnessesCmp[$genomeId] = $fitness;
        }

        $this->assertEquals($fitnesses, $fitnessesCmp);
    }

    public function testEvaluationBadFitnessFunction()
    {
        $neat = new $this->className();

        $neat
            ->nbInputs(3)
            ->nbOutputs(2)
            ->populationSize(3)
            ->initializationMethod([NeatUtils::class, 'initFullyConnected'])
            ->fitnessFunction([$this, 'badFitnessFunction']);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatException::class);
        $neat->evaluation();
    }
/*
    public function testRunOnce()
    {
        $neat = new $this->className();

        $neat
            ->nbInputs(3)
            ->nbOutputs(2)
            ->populationSize(3)
            ->initializationMethod([NEAT::class, 'initFullyConnected']);

        $neat->runOnce();
    }

    /*
    public function testXor()
    {
        $neat = new NEAT();

        // Create a genome pool with 50 genomes having 2 inputs and 1 output
        $neat
            ->nbInputs(2)
            ->nbOutputs(1)
            ->populationSize(5);

        // Evaluation settings
        $neat
            // The script should run for 100 generations max
            ->maxGenerations(2)
            // The script will stop when the minimum fitness reaches 0.05
            ->fitnessThreshold('min', 0.05)
            // Set the fitness function
            ->fitnessFunction([$this, 'xorFitnessFunction']);

        // Run the algorithm
        $neat->run();

        $this->assertTrue(true);
    }
    */
    public function xorFitnessFunction(iterable &$genomes): void
    {
        $trainingData = [
            [
                'input' => [0, 0],
                // 0 XOR 0 => 0
                'output' => 0,
            ],
            [
                'input' => [0, 1],
                // 0 XOR 1 => 1
                'output' => 1,
            ],
            [
                'input' => [1, 0],
                // 1 XOR 0 => 1
                'output' => 1,
            ],
            [
                'input' => [1, 1],
                // 1 XOR 1 => 0
                'output' => 0,
            ],
        ];

        // Calculate fitness for all genomes
        foreach ($genomes as &$genome) {
            // Calculate fitness based on expected outputs
            $fitness = 0;
            foreach ($trainingData as $trainingSample) {
                // Compute the output
                $output = $genome->activate($trainingSample['input']);
                // Compute the difference between output and expected value
                $fitness += abs($output[0] - $trainingSample['output']);
            }

            // $fitness is now equal to the sum of errors for all training data

            // Set the fitness
            $genome->setFitness($fitness);
        }
    }

    public function badFitnessFunction(iterable &$genomes): void
    {
    }

    /**
     * @depends testConstruct
     */
    /*
    public function testCurrentGeneration(NeatConfigInterface $neat)
    {
        $this->assertEquals($neat->currentGeneration(), 0);
    }

    public function testGenePool()
    {
        $neat = new NEAT();
        $this->assertNull($neat->getGenePool());
        $genePool = new GenePool();
        $neat->importGenePool($genePool);
        $this->assertSame($genePool, $neat->getGenePool());
    }

    public function testGenomePool()
    {
        $neat = new NEAT();
        $this->assertNull($neat->getGenePool());
        $genomePool = new GenomePool();
        $neat->importGenomePool($genomePool);
        $this->assertSame($genomePool, $neat->getGenomePool());
    }*/
}
