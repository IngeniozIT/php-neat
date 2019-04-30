<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\NEAT;
use IngeniozIT\NEAT\Interfaces\GenePoolInterface;

class NeatTest extends TestCase
{
    public function testGenePoolNoInputsNumber()
    {
        $neat = new NEAT();

        $neat->nbOutputs(2);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatException::class);
        $neat->createGenePool();
    }

    public function testGenePoolNoOutputsNumber()
    {
        $neat = new NEAT();

        $neat->nbInputs(3);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatException::class);
        $neat->createGenePool();
    }

    public function testFullyConnectedGenePool()
    {
        $neat = new NEAT();

        $neat->fullyConnected(true);

        $neat->nbInputs(3);
        $neat->nbOutputs(2);

        $neat->createGenePool();

        $genePool = $neat->getGenePool();

        $inputGenes = $genePool->getInputGenes();
        $outputGenes = $genePool->getOutputGenes();
        $connexionGenes = $genePool->getConnexionGenes();

        $this->assertEquals(3, count($inputGenes));
        $this->assertEquals(2, count($outputGenes));
        $this->assertEquals(6, count($connexionGenes));

        foreach ($inputGenes as $inId) {
            $this->assertTrue($genePool->nodeGeneExists($inId));
            $this->assertTrue($genePool->nodeGeneExists($inId, GenePoolInterface::NODE_INPUT));
            foreach ($outputGenes as $outId) {
                $this->assertEquals([$inId, $outId], $connexionGenes[$genePool->getConnexionGeneId($inId, $outId)]);
            }
        }
        foreach ($outputGenes as $outId) {
            $this->assertTrue($genePool->nodeGeneExists($outId));
            $this->assertTrue($genePool->nodeGeneExists($outId, GenePoolInterface::NODE_OUTPUT));
        }
        $this->assertEquals(6, count($genePool->getConnexionGenes()));
    }

    public function testFSGenePool()
    {
        $neat = new NEAT();

        $neat->fullyConnected(false);

        $neat->nbInputs(3);
        $neat->nbOutputs(2);

        $neat->createGenePool();

        $genePool = $neat->getGenePool();

        $this->assertEquals(3, count($genePool->getInputGenes()));
        $this->assertEquals(2, count($genePool->getOutputGenes()));
        $this->assertEquals(0, count($genePool->getConnexionGenes()));
    }

    public function testGenomePoolNoInputsNumber()
    {
        $neat = new NEAT();

        $neat->nbOutputs(2);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatException::class);
        $neat->createGenomePool();
    }

    public function testGenomePoolNoOutputsNumber()
    {
        $neat = new NEAT();

        $neat->nbInputs(3);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatException::class);
        $neat->createGenomePool();
    }

    public function testFullyConnectedGenomePool()
    {
        $neat = new NEAT();

        $neat->fullyConnected(true);

        $neat->nbInputs(3);
        $neat->nbOutputs(2);
        $neat->populationSize(3);

        $neat->createGenomePool();

        $genePool = $neat->getGenePool();
        $inputGenes = $genePool->getInputGenes();
        $outputGenes = $genePool->getOutputGenes();
        $connexionGenes = $genePool->getConnexionGenes();

        $genomes = $neat->getGenomePool()->getGenomes();

        $this->assertEquals(3, count($genomes));

        foreach ($genomes as $genome) {
            foreach ($inputGenes as $inId) {
                $this->assertTrue($genome->hasNode($inId));
            }
            foreach ($outputGenes as $outId) {
                $this->assertTrue($genome->hasNode($outId));
            }
            foreach ($connexionGenes as $connId => $connexion) {
                $this->assertTrue($genome->hasConnexion($connId));
            }
        }
    }

    public function testXor()
    {
        $neat = new NEAT();

        // Create a genome pool with 50 genomes having 2 inputs and 1 output
        $neat
            ->nbInputs(2)
            ->nbOutputs(1)
            ->populationSize(50);

        // Evaluation settings
        $neat
            // The script should run for 100 generations max
            ->maxGenerations(100)
            // The script will stop when the minimum fitness reaches 0.05
            ->fitnessThreshold('min', 0.05)
            // Set the fitness function
            ->fitnessFunction([$this, 'xorFitnessFunction']);

        // Run the algorithm
        $neat->run();

        /**
         * @todo placeholder test
         */
        $this->assertTrue(true);
    }

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
}
