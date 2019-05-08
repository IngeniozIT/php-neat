<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\NeatUtils;
use IngeniozIT\NEAT\NEAT;
use IngeniozIT\NEAT\Interfaces\GenomePoolInterface;
use IngeniozIT\NEAT\GenomePool;
use IngeniozIT\NEAT\GenePool;

/**
 * @coversDefaultClass \IngeniozIT\NEAT\NeatUtils
 */
class NeatUtilsTest extends TestCase
{
    protected $className = NEAT::class;

    public function testNotConnected()
    {
        $neat = new $this->className();

        $neat
            ->nbInputs(3)
            ->nbOutputs(2)
            ->populationSize(3)
            ->initializationMethod([NeatUtils::class, 'initNotConnected']);

        $genePool = $neat->pool()->genePool();

        foreach ($neat->pool()->genomes() as $genome) {
            $this->assertEquals(0, count($genome->connexions()));
        }
    }

    public function testPartiallyConnected()
    {
        $neat = new $this->className();

        $neat
            ->nbInputs(3)
            ->nbOutputs(2)
            ->populationSize(3)
            ->initializationMethod([NeatUtils::class, 'initPartiallyConnected']);

        $genePool = $neat->pool()->genePool();

        foreach ($neat->pool()->genomes() as $genome) {
            $this->assertEquals(1, count($genome->connexions()));
        }
    }

    public function testFullyConnected()
    {
        $neat = new $this->className();

        $neat
            ->nbInputs(3)
            ->nbOutputs(2)
            ->populationSize(3)
            ->initializationMethod([NeatUtils::class, 'initFullyConnected']);

        $genePool = $neat->pool()->genePool();

        $inputGenes = $genePool->inputGenes();
        $outputGenes = $genePool->outputGenes();

        $connexionIds = [];
        foreach ($inputGenes as $inId) {
            foreach ($outputGenes as $outId) {
                $connexionIds[] = $genePool->connexionGeneId($inId, $outId);
            }
        }

        foreach ($neat->pool()->genomes() as $genome) {
            $connexions = $genome->connexions();
            $this->assertEquals(6, count($connexions));
            $validGenes = true;
            foreach ($connexionIds as $connId) {
                $validGenes = $validGenes && array_key_exists($connId, $connexions);
            }
            $this->assertTrue($validGenes);
        }
    }
}
