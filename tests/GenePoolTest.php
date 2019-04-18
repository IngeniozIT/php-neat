<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\GenePool;
use IngeniozIT\NEAT\Interfaces\GenePoolInterface;

class GenePoolTest extends TestCase
{
    protected $className = GenePool::class;

    public function testAddInputGene()
    {
        $genePool = new $this->className();

        $genePool->addInputGene();

        $this->assertEquals([0], $genePool->getInputGenes());
        $this->assertEquals([], $genePool->getOutputGenes());
        $this->assertEquals([], $genePool->getHiddenGenes());
        $this->assertEquals([
            0 => GenePoolInterface::NODE_INPUT
        ], $genePool->getNodeGenes());
        $this->assertTrue($genePool->nodeGeneExists(0));
        $this->assertTrue($genePool->nodeGeneExists(0, GenePoolInterface::NODE_INPUT));
    }

    public function testAddOutputGene()
    {
        $genePool = new $this->className();

        $genePool->addOutputGene();

        $this->assertEquals([], $genePool->getInputGenes());
        $this->assertEquals([0], $genePool->getOutputGenes());
        $this->assertEquals([], $genePool->getHiddenGenes());
        $this->assertEquals([
            0 => GenePoolInterface::NODE_OUTPUT
        ], $genePool->getNodeGenes());
        $this->assertTrue($genePool->nodeGeneExists(0));
        $this->assertTrue($genePool->nodeGeneExists(0, GenePoolInterface::NODE_OUTPUT));
    }

    public function testAddHiddenGene()
    {
        $genePool = new $this->className();

        $genePool->addHiddenGene();

        $this->assertEquals([], $genePool->getInputGenes());
        $this->assertEquals([], $genePool->getOutputGenes());
        $this->assertEquals([0], $genePool->getHiddenGenes());
        $this->assertEquals([
            0 => GenePoolInterface::NODE_HIDDEN
        ], $genePool->getNodeGenes());
        $this->assertTrue($genePool->nodeGeneExists(0));
        $this->assertTrue($genePool->nodeGeneExists(0, GenePoolInterface::NODE_HIDDEN));
    }

    public function testAddMultipleGenes()
    {
        $genePool = new $this->className();

        $genePool->addInputGene();
        $genePool->addInputGene();
        $genePool->addInputGene();
        $genePool->addOutputGene();
        $genePool->addHiddenGene();
        $genePool->addHiddenGene();

        $this->assertEquals([0, 1, 2], $genePool->getInputGenes());
        $this->assertEquals([3], $genePool->getOutputGenes());
        $this->assertEquals([4, 5], $genePool->getHiddenGenes());
        $this->assertEquals([
            0 => GenePoolInterface::NODE_INPUT,
            1 => GenePoolInterface::NODE_INPUT,
            2 => GenePoolInterface::NODE_INPUT,
            3 => GenePoolInterface::NODE_OUTPUT,
            4 => GenePoolInterface::NODE_HIDDEN,
            5 => GenePoolInterface::NODE_HIDDEN
        ], $genePool->getNodeGenes());
    }

    public function testAddConnexionGeneMissingIn()
    {
        $genePool = new $this->className();

        $genePool->addInputGene();

        $this->expectException(\IngeniozIT\NEAT\Exceptions\GenePoolException::class);
        $genePool->addConnexionGene(1, 0);
    }

    public function testAddConnexionGeneMissingOut()
    {
        $genePool = new $this->className();

        $genePool->addInputGene();

        $this->expectException(\IngeniozIT\NEAT\Exceptions\GenePoolException::class);
        $genePool->addConnexionGene(0, 1);
    }

    public function testAddConnexionGene()
    {
        $genePool = new $this->className();

        $genePool->addInputGene();
        $genePool->addOutputGene();

        $genePool->addConnexionGene(0, 1);

        $this->assertEquals([
            0 => [0, 1]
        ], $genePool->getConnexionGenes());
        $this->assertEquals(0, $genePool->getConnexionGeneId(0, 1));
    }

    public function testAddConnexionGeneDuplicate()
    {
        $genePool = new $this->className();

        $genePool->addInputGene();
        $genePool->addOutputGene();

        $genePool->addConnexionGene(0, 1);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\GenePoolException::class);
        $genePool->addConnexionGene(0, 1);
    }

    public function testAddMultipleConnexionGenes()
    {
        $genePool = new $this->className();

        $genePool->addInputGene();
        $genePool->addInputGene();
        $genePool->addInputGene();
        $genePool->addOutputGene();
        $genePool->addHiddenGene();

        $genePool->addConnexionGene(0, 3);
        $genePool->addConnexionGene(3, 0);
        $genePool->addConnexionGene(1, 3);
        $genePool->addConnexionGene(2, 3);
        $genePool->addConnexionGene(0, 4);
        $genePool->addConnexionGene(4, 3);

        $this->assertEquals([
            0 => [0, 3],
            1 => [3, 0],
            2 => [1, 3],
            3 => [2, 3],
            4 => [0, 4],
            5 => [4, 3],
        ], $genePool->getConnexionGenes());
        $this->assertEquals(0, $genePool->getConnexionGeneId(0, 3));
        $this->assertEquals(1, $genePool->getConnexionGeneId(3, 0));
        $this->assertEquals(2, $genePool->getConnexionGeneId(1, 3));
        $this->assertEquals(3, $genePool->getConnexionGeneId(2, 3));
        $this->assertEquals(4, $genePool->getConnexionGeneId(0, 4));
        $this->assertEquals(5, $genePool->getConnexionGeneId(4, 3));
    }

    public function testGetConnexionGeneIdCreate()
    {
        $genePool = new $this->className();

        $genePool->addInputGene();
        $genePool->addOutputGene();

        $this->assertEquals(0, $genePool->getConnexionGeneId(0, 1));
        $this->assertEquals(1, $genePool->getConnexionGeneId(1, 0));
    }
}
