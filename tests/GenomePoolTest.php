<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\Interfaces\GenomePoolInterface;
use IngeniozIT\NEAT\GenomePool;

use IngeniozIT\NEAT\GenePool;
use IngeniozIT\NEAT\Genome;

class GenomePoolTest extends TestCase
{
    protected $classname = GenomePool::class;

    public function testConstruct()
    {
        $genomePool = new $this->classname(new GenePool());

        $this->assertInstanceOf($this->classname, $genomePool);

        return $genomePool;
    }

    public function testGetGenePool()
    {
        $genePool = new GenePool();
        $genomePool = new $this->classname($genePool);

        $this->assertSame($genomePool->getGenePool(), $genePool);
    }

    /**
     * @depends testConstruct
     */
    public function testAddGenome(GenomePoolInterface $genomePool)
    {
        $genome = new Genome([], []);
        $genome2 = new Genome([], []);

        $genomePool
            ->addGenome($genome)
            ->addGenome($genome2);

        $this->assertSame([$genome, $genome2], $genomePool->genomes());

        return $genomePool;
    }

    /**
     * @depends testAddGenome
     */
    public function testSpecies(GenomePoolInterface $genomePool)
    {
        $genomes = $genomePool->genomes();

        $this->assertEquals([], $genomePool->getSpecies());

        $genomePool->assignGenomesToSpecies([1, 0], 42);

        $this->assertEquals([
            42 => [0, 1]
        ], $genomePool->getSpecies());
        $this->assertEquals(42, $genomes[0]->getSpecies());
        $this->assertEquals(42, $genomes[1]->getSpecies());

        $genomePool->assignGenomesToSpecies([0, 1], null);
        $this->assertEquals([], $genomePool->getSpecies());
        foreach ($genomes as $genome) {
            $this->assertNull($genome->getSpecies());
        }

        $genomePool
            ->assignGenomesToSpecies([0], 42)
            ->assignGenomesToSpecies([1], 21);

        $this->assertEquals([
            21 => [1],
            42 => [0]
        ], $genomePool->getSpecies());
        $this->assertEquals(42, $genomes[0]->getSpecies());
        $this->assertEquals(21, $genomes[1]->getSpecies());

        return $genomePool;
    }

    /**
     * @depends testAddGenome
     */
    public function testResetSpecies(GenomePoolInterface $genomePool)
    {
        $genomePool->resetSpecies();
        $this->assertEquals([], $genomePool->getSpecies());

        foreach ($genomePool->genomes() as $genome) {
            $this->assertNull($genome->getSpecies());
        }
    }

    /**
     * @depends testSpecies
     */
    public function testRemoveGenome(GenomePoolInterface $genomePool)
    {
        $genomes = $genomePool->genomes();

        $genomePool->removeGenome(0);
        $this->assertSame([1 => $genomes[1]], $genomePool->genomes());

        $this->assertNull($genomes[0]->getSpecies());
    }
}
