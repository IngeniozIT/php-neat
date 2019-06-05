<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Tests\Agents;

use PHPUnit\Framework\TestCase;

use IngeniozIT\Math\ActivationFunction;

/**
 * @coversDefaultClass \IngeniozIT\Neat\Agents\AgentFactory
 */
class AgentFactoryTest extends TestCase
{
    protected function getObject()
    {
        return new \IngeniozIT\Neat\Agents\AgentFactory();
    }

    public function testConstruct()
    {
        $obj = $this->getObject();
        $this->assertTrue($obj instanceof \IngeniozIT\Neat\Agents\AgentFactory);
    }

    public function testCreateGenome()
    {
        $obj = $this->getObject();

        // Nodes
        $nodeGenes = [];
        for ($i = 1; $i <= 3; ++$i) {
            $nodeGenes[] = new \IngeniozIT\Neat\Genotype\NodeGene(
                $i,
                \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR,
                [ActivationFunction::class, 'binaryStep'],
                'array_sum'
            );
        }
        $nodeGenes[] = new \IngeniozIT\Neat\Genotype\NodeGene(
            4,
            \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN,
            [ActivationFunction::class, 'binaryStep'],
            'array_sum'
        );
        $nodeGenes[] = new \IngeniozIT\Neat\Genotype\NodeGene(
            5,
            \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT,
            [ActivationFunction::class, 'binaryStep'],
            'array_sum'
        );

        // Connexion genes
        $connectGenes = [];
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(1, 1, 4, -5, false);
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(2, 2, 4, 10, false);
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(3, 2, 5, -10, false);
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(4, 3, 4, 10, false);
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(5, 3, 5, -10, false);
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(6, 4, 5, 15, false);

        $xor = [
            [[1, 0, 0], [0]],
            [[1, 0, 1], [1]],
            [[1, 1, 0], [1]],
            [[1, 1, 1], [0]],
        ];

        $genome = $obj->createGenome($nodeGenes, $connectGenes);

        $this->assertInstanceOf(\IngeniozIT\Neat\Agents\Interfaces\GenomeInterface::class, $genome);

        foreach ($xor as $xorCase) {
            $this->assertEquals($xorCase[1], $genome->activate($xorCase[0]));
        }

        $this->assertEquals([1, -5, 1, 10, 1, -10, 1, 10, 1, -10, 1, 15], $genome->toVector(5, 6, [[ActivationFunction::class, 'binaryStep']], ['array_sum']));
    }

    public function testCreateAgent()
    {
        $obj = $this->getObject();

        // Nodes
        $nodeGenes = [];
        for ($i = 1; $i <= 3; ++$i) {
            $nodeGenes[] = new \IngeniozIT\Neat\Genotype\NodeGene(
                $i,
                \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR,
                [ActivationFunction::class, 'binaryStep'],
                'array_sum'
            );
        }
        $nodeGenes[] = new \IngeniozIT\Neat\Genotype\NodeGene(
            4,
            \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN,
            [ActivationFunction::class, 'binaryStep'],
            'array_sum'
        );
        $nodeGenes[] = new \IngeniozIT\Neat\Genotype\NodeGene(
            5,
            \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT,
            [ActivationFunction::class, 'binaryStep'],
            'array_sum'
        );

        // Connexion genes
        $connectGenes = [];
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(1, 1, 4, -5, false);
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(2, 2, 4, 10, false);
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(3, 2, 5, -10, false);
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(4, 3, 4, 10, false);
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(5, 3, 5, -10, false);
        $connectGenes[] = new \IngeniozIT\Neat\Genotype\ConnectGene(6, 4, 5, 15, false);

        $xor = [
            [[1, 0, 0], [0]],
            [[1, 0, 1], [1]],
            [[1, 1, 0], [1]],
            [[1, 1, 1], [0]],
        ];

        $genome = $obj->createAgent($nodeGenes, $connectGenes);

        $this->assertInstanceOf(\IngeniozIT\Neat\Agents\Interfaces\AgentInterface::class, $genome);

        foreach ($xor as $xorCase) {
            $this->assertEquals($xorCase[1], $genome->activate($xorCase[0]));
        }

        $this->assertEquals([1, -5, 1, 10, 1, -10, 1, 10, 1, -10, 1, 15], $genome->toVector(5, 6, [[ActivationFunction::class, 'binaryStep']], ['array_sum']));
    }
}
