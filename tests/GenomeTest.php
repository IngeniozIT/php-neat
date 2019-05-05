<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\Genome;
use IngeniozIT\NEAT\Interfaces\GenomeInterface;
use IngeniozIT\Math\ActivationFunction;

/**
 * @coversDefaultClass \IngeniozIT\NEAT\Genome
 */
class GenomeTest extends TestCase
{
    protected $className = Genome::class;

    public function testBadActivationFunction()
    {
        $this->expectException(\IngeniozIT\NEAT\Exceptions\GenomeException::class);
        $genome = new $this->className(
            [],
            [0 => 'array_sum']
        );
        $genome->addInputNode(1, 0, 0);
    }

    public function testBadAggregationFunction()
    {
        $this->expectException(\IngeniozIT\NEAT\Exceptions\GenomeException::class);
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            []
        );
        $genome->addInputNode(1, 0, 0);
    }

    public function testAddExistingConnexion()
    {
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            [0 => 'array_sum']
        );

        $genome->addInputNode(1, 0, 0);
        $genome->addOutputNode(2, 0, 0);
        $genome->addConnexion(1, 1, 2, 1);
        $genome->addConnexion(2, 1, 2, 1);
        $this->expectException(\IngeniozIT\NEAT\Exceptions\GenomeException::class);
        $genome->addConnexion(1, 1, 2, 1);
    }

    public function testHasConnexion()
    {
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            [0 => 'array_sum']
        );
        $genome->addInputNode(1, 0, 0);
        $genome->addOutputNode(2, 0, 0);
        $genome->addConnexion(42, 1, 2, 1);

        $this->assertTrue($genome->hasConnexion(42));
        $this->assertFalse($genome->hasConnexion(1));
    }

    public function testCheckConnexion()
    {
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            [0 => 'array_sum']
        );

        $this->expectException(\IngeniozIT\NEAT\Exceptions\GenomeException::class);
        $genome->checkConnexion(42);
    }

    public function testAddExistingNode()
    {
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            [0 => 'array_sum']
        );

        $genome->addInputNode(1, 0, 0);
        $this->expectException(\IngeniozIT\NEAT\Exceptions\GenomeException::class);
        $genome->addInputNode(1, 0, 0);
    }

    public function testHasNode()
    {
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            [0 => 'array_sum']
        );
        $genome->addInputNode(42, 0, 0);

        $this->assertTrue($genome->hasNode(42));
        $this->assertFalse($genome->hasNode(1));
    }

    public function testCheckNode()
    {
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            [0 => 'array_sum']
        );

        $this->expectException(\IngeniozIT\NEAT\Exceptions\GenomeException::class);
        $genome->checkNode(42);
    }

    public function testGetVector()
    {
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            [0 => 'array_sum']
        );

        $genome->addInputNode(1, 0, 0);
        $genome->addHiddenNode(2, 0, 0);
        $genome->addHiddenNode(3, 0, 0);
        $genome->addHiddenNode(4, 0, 0);
        $genome->addOutputNode(5, 0, 0);

        $genome->addConnexion(1, 1, 2, 1);
        $genome->addConnexion(2, 2, 3, 500);
        $genome->addConnexion(3, 3, 4, -2000);
        $genome->addConnexion(4, 4, 5, 42);

        $this->assertEquals(
            [
                1 => 1,
                2 => 500,
                3 => -2000,
                4 => 42,
            ],
            $genome->vector()
        );
    }

    public function testFitness()
    {
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            [0 => 'array_sum']
        );

        $this->assertNull($genome->fitness());
        $genome->setFitness(42.42);
        $this->assertEquals(42.42, $genome->fitness());
    }

    public function testSpecies()
    {
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            [0 => 'array_sum']
        );

        $this->assertNull($genome->species());
        $genome->setSpecies(42);
        $this->assertEquals(42, $genome->species());
    }

    public function testSnakeNetwork()
    {
        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'identity']],
            [0 => 'array_sum']
        );

        $genome->addInputNode(1, 0, 0);
        $genome->addHiddenNode(2, 0, 0);
        $genome->addHiddenNode(3, 0, 0);
        $genome->addHiddenNode(4, 0, 0);
        $genome->addOutputNode(5, 0, 0);

        $genome->addConnexion(1, 1, 2, 1);
        $genome->addConnexion(2, 2, 3, 1);
        $genome->addConnexion(3, 3, 4, 1);
        $genome->addConnexion(4, 4, 5, 1);

        $this->assertEquals([1], $genome->activate([1]));

        return $genome;
    }

    /**
     * @depends testSnakeNetwork
     */
    public function testDisabledNode(GenomeInterface $genome)
    {
        $genome->disableNode(2);
        $this->assertEquals([0], $genome->activate([1]));

        $genome->enableNode(2);
        $this->assertEquals([1], $genome->activate([1]));

        $genome->toggleNode(1);
        $this->assertEquals([0], $genome->activate([1]));
    }

    public function testOr()
    {
        $orCases = [
            [0, 0, 0],
            [0, 1, 1],
            [1, 0, 1],
            [1, 1, 1],
        ];

        $genome = new $this->className(
            [
                0 => [ActivationFunction::class, 'identity'],
                1 => [ActivationFunction::class, 'binaryStep'],
            ],
            [0 => 'array_sum']
        );

        // OR

        $genome->addInputNode(1, 0, 0);
        $genome->addInputNode(2, 0, 0);
        $genome->addOutputNode(3, 1, 0);

        $genome->addConnexion(1, 1, 3, 1);
        $genome->addConnexion(2, 2, 3, 1);

        foreach ($orCases as $case) {
            $this->assertEquals($genome->activate([$case[0], $case[1]])[0], $case[2]);
        }
    }

    public function testAnd()
    {
        $andCases = [
            [0, 0, 0],
            [0, 1, 0],
            [1, 0, 0],
            [1, 1, 1],
        ];

        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'binaryStep']],
            [0 => 'array_sum']
        );

        $genome->addInputNode(1, 0, 0);
        $genome->addInputNode(2, 0, 0);
        $genome->addInputNode(3, 0, 0);
        $genome->addOutputNode(4, 0, 0);

        $genome->addConnexion(1, 1, 4, 1);
        $genome->addConnexion(2, 2, 4, 1);
        $genome->addConnexion(3, 3, 4, -1.5);

        foreach ($andCases as $case) {
            $this->assertEquals($case[2], $genome->activate([$case[0], $case[1], 1])[0]);
        }

        return $genome;
    }

    /**
     * @depends testAnd
     */
    public function testNand(GenomeInterface $genome)
    {
        $nandCases = [
            [0, 0, 1],
            [0, 1, 1],
            [1, 0, 1],
            [1, 1, 0],
        ];

        $genome->setConnexionWeight(1, -$genome->getConnexionWeight(1));
        $genome->setConnexionWeight(2, -$genome->getConnexionWeight(2));
        $genome->setConnexionWeight(3, -$genome->getConnexionWeight(3));

        foreach ($nandCases as $case) {
            $this->assertEquals($case[2], $genome->activate([$case[0], $case[1], 1])[0]);
        }
    }

    public function testXor()
    {
        $xorCases = [
            [0, 0, 0],
            [0, 1, 1],
            [1, 0, 1],
            [1, 1, 0],
        ];

        $genome = new $this->className(
            [0 => [ActivationFunction::class, 'binaryStep']],
            [0 => 'array_sum']
        );

        $genome->addInputNode(1, 0, 0);
        $genome->addInputNode(2, 0, 0);
        $genome->addInputNode(3, 0, 0);
        $genome->addOutputNode(4, 0, 0);
        $genome->addHiddenNode(5, 0, 0);
        $genome->addHiddenNode(6, 0, 0);

        // OR
        $genome->addConnexion(1, 1, 5, 1);
        $genome->addConnexion(2, 2, 5, 1);

        // NAND
        $genome->addConnexion(3, 1, 6, -1);
        $genome->addConnexion(4, 2, 6, -1);
        $genome->addConnexion(5, 3, 6, 1.5);

        // OR AND NAND => XOR
        $genome->addConnexion(6, 3, 4, -1.5);
        $genome->addConnexion(7, 5, 4, 1);
        $genome->addConnexion(8, 6, 4, 1);

        foreach ($xorCases as $case) {
            $this->assertEquals($case[2], $genome->activate([$case[0], $case[1], 1])[0]);
        }
    }
}
