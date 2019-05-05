<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\Interfaces\GenomePoolInterface;
use IngeniozIT\NEAT\GenomePool;

use IngeniozIT\NEAT\GenePool;
use IngeniozIT\NEAT\Genome;
use IngeniozIT\Math\Random;

/**
 * @coversDefaultClass \IngeniozIT\NEAT\GenomePool
 */
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

        $this->assertSame($genomePool->genePool(), $genePool);
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

    public function testGetVectors()
    {
        $genomePool = new $this->classname(new GenePool());

        $vectors = [];
        for ($i = 0; $i < 5; ++$i) {
            $genome = new Genome(
                [
                    0 => [ActivationFunction::class, 'identity'],
                ],
                [
                    0 => 'array_sum',
                ]
            );

            $genome->addInputNode(1, 0, 0);
            $genome->addHiddenNode(2, 0, 0);
            $genome->addHiddenNode(3, 0, 0);
            $genome->addHiddenNode(4, 0, 0);
            $genome->addOutputNode(5, 0, 0);

            $genome->addConnexion(1, 1, 2, Random::frand(-10, 10));
            $genome->addConnexion(2, 2, 3, Random::frand(-10, 10));
            $genome->addConnexion(3, 3, 4, Random::frand(-10, 10));
            $genome->addConnexion(4, 4, 5, Random::frand(-10, 10));

            $vectors[] = $genome->vector();
            $genomePool->addGenome($genome);
        }

        $this->assertEquals($vectors, $genomePool->getVectors());
    }

    /**
     * @depends testAddGenome
     */
    public function testSpecies(GenomePoolInterface $genomePool)
    {
        $genomes = $genomePool->genomes();

        $this->assertEquals([], $genomePool->getSpecies());

        $genomePool->assignGenomesToSpecies([1, 0], 42);

        $this->assertEquals(
            [
                42 => [0, 1]
            ],
            $genomePool->getSpecies()
        );
        $this->assertEquals(42, $genomes[0]->species());
        $this->assertEquals(42, $genomes[1]->species());

        $genomePool->assignGenomesToSpecies([0, 1], null);
        $this->assertEquals([], $genomePool->getSpecies());
        foreach ($genomes as $genome) {
            $this->assertNull($genome->species());
        }

        $genomePool
            ->assignGenomesToSpecies([0], 42)
            ->assignGenomesToSpecies([1], 21);

        $this->assertEquals(
            [
                21 => [1],
                42 => [0]
            ],
            $genomePool->getSpecies()
        );
        $this->assertEquals(42, $genomes[0]->species());
        $this->assertEquals(21, $genomes[1]->species());

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
            $this->assertNull($genome->species());
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

        $this->assertNull($genomes[0]->species());
    }
}
