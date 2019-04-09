<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\NEAT;

class ConfigTest extends TestCase
{
    public function testConstruct()
    {
        $neat = new NEAT();

        $this->assertInstanceOf(NEAT::class, $neat);

        return $neat;
    }

    /**
     * @depends testConstruct
     */
    public function testNbInputs(NEAT $neat)
    {
        $neat->nbInputs(2);

        $this->assertEquals($neat->getNbInputs(), 2);

        return $neat;
    }

    /**
     * @depends testConstruct
     */
    public function testNbOuputs(NEAT $neat)
    {
        $neat->nbOutputs(1);

        $this->assertEquals($neat->getNbOutputs(), 1);

        return $neat;
    }

    /**
     * @depends testConstruct
     */
    public function testPopulationSize(NEAT $neat)
    {
        $neat->populationSize(50);

        $this->assertEquals($neat->getPopulationSize(), 50);

        return $neat;
    }

    /**
     * @depends testConstruct
     */
    public function testMaxGenerations(NEAT $neat)
    {
        $neat->maxGenerations(100);

        $this->assertEquals($neat->getMaxGenerations(), 100);

        return $neat;
    }

    /**
     * @depends testConstruct
     */
    public function testFitnessThreshold(NEAT $neat)
    {
        $neat->fitnessThreshold('min', 0.05);
        $this->assertEquals($neat->getFitnessThreshold(), ['min', 0.05]);

        return $neat;
    }

    /**
     * @depends testConstruct
     */
    public function testFitnessFunction(NEAT $neat)
    {
        $neat->fitnessFunction([$this, 'sampleFitnessFunction']);

        $this->assertSame($neat->getFitnessFunction(), [$this, 'sampleFitnessFunction']);

        return $neat;
    }

    /**
     * @depends testConstruct
     */
    public function testCurrentGeneration(NEAT $neat)
    {
        $this->assertEquals($neat->currentGeneration(), 0);
    }

    public function testAutoPopulationSize()
    {
        $neat = new NEAT();
        $neat->nbInputs(20);
        $neat->nbOutputs(10);
        $this->assertEquals($neat->getPopulationSize(), 400);

        $neat = new NEAT();
        $neat->nbOutputs(10);
        $neat->nbInputs(20);
        $this->assertEquals($neat->getPopulationSize(), 400);
    }

    /**
     * @depends testConstruct
     * @expectedException Exception
     */
    public function testBadFitnessThreshold(NEAT $neat)
    {
        $neat->fitnessThreshold('min', 42.0);
        $this->assertEquals($neat->getFitnessThreshold(), ['min', 42.0]);

        $neat->fitnessThreshold('max', 84.0);
        $this->assertEquals($neat->getFitnessThreshold(), ['max', 84.0]);

        $neat->fitnessThreshold('foo', 0.05);
    }


    /**
     * @depends testConstruct
     */
    public function testFullyConnected(NEAT $neat)
    {
        $this->assertTrue($neat->getFullyConnected());

        $neat->fullyConnected(false);
        $this->assertFalse($neat->getFullyConnected());

        $neat->fullyConnected(true);
        $this->assertTrue($neat->getFullyConnected());
    }

    public function sampleFitnessFunction(array &$genomes): void
    {
    }
}
