<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\NEAT;

class NeatTest extends TestCase
{
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

    public function testMultipleXor()
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

    public function xorFitnessFunction(array &$genomes): void
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
            foreach ($this->trainingData as $trainingSample) {
                // Compute the output
                $output = $genome->activate($trainingSample['input']);
                // Compute the difference between output and expected value
                $fitness += abs($expectedValue - $trainingSample['output']);
            }

            // $fitness is now equal to the sum of errors for all training data

            // Set the fitness
            $genome->setFitness($fitness);
        }
    }
}
