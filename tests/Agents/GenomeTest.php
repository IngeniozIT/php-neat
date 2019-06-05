<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Tests\Agents;

use PHPUnit\Framework\TestCase;

use IngeniozIT\Math\ActivationFunction;

/**
 * @coversDefaultClass \IngeniozIT\Neat\Agents\Genome
 */
class GenomeTest extends TestCase
{
    protected function getObject()
    {
        return new \IngeniozIT\Neat\Agents\Genome();
    }

    public function testConstruct()
    {
        $obj = $this->getObject();
        $this->assertTrue($obj instanceof \IngeniozIT\Neat\Agents\Genome);
    }

    public function testEmpty()
    {
        $obj = $this->getObject();

        $this->assertEmpty($obj->nodeGenes());
        $this->assertEmpty($obj->connectGenes());
        $this->assertEmpty($obj->activate([1, 2, 3]));
        $this->assertEmpty($obj->toVector(0, 0, [], []));
    }

    public function testAddNodeGene()
    {
        $obj = $this->getObject();
        $nodeGene = new \IngeniozIT\Neat\Genotype\NodeGene(42, 1, 'sqrt', 'array_sum');
        $nodeGene2 = new \IngeniozIT\Neat\Genotype\NodeGene(21, 2, 'sqrt', 'array_sum');
        $nodeGene3 = new \IngeniozIT\Neat\Genotype\NodeGene(84, 1, 'sqrt', 'array_sum');

        $obj->addNodeGene($nodeGene);
        $this->assertSame([42 => $nodeGene], $obj->nodeGenes());
        $this->assertEquals(42, $obj->maxNodeInnovation());
        $obj->addNodeGene($nodeGene2);
        $this->assertSame([21 => $nodeGene2, 42 => $nodeGene], $obj->nodeGenes());
        $this->assertEquals(42, $obj->maxNodeInnovation());
        $obj->addNodeGene($nodeGene3);
        $this->assertSame([21 => $nodeGene2, 42 => $nodeGene, 84 => $nodeGene3], $obj->nodeGenes());
        $this->assertEquals(84, $obj->maxNodeInnovation());
    }

    public function testAddExistingNodeGene()
    {
        $obj = $this->getObject();
        $nodeGene = new \IngeniozIT\Neat\Genotype\NodeGene(42, 1, 'sqrt', 'array_sum');
        $nodeGene2 = new \IngeniozIT\Neat\Genotype\NodeGene(42, 2, 'sqrt', 'array_sum');

        $obj->addNodeGene($nodeGene);
        $this->expectException(\IngeniozIT\Neat\Exceptions\RuntimeException::class);
        $obj->addNodeGene($nodeGene2);
    }

    public function testAddNodeGeneModifyGene()
    {
        $obj = $this->getObject();
        $nodeGene = new \IngeniozIT\Neat\Genotype\NodeGene(42, 1, 'sqrt', 'array_sum');
        $nodeGene2 = new \IngeniozIT\Neat\Genotype\NodeGene(21, 2, 'sqrt', 'array_sum');

        $obj->addNodeGene($nodeGene);
        $obj->addNodeGene($nodeGene2);

        $nodeGene->setAggregationFunction('array_product');
        $nodeGene->setActivationFunction([ActivationFunction::class, 'identity']);
        $this->assertEquals('array_product', $obj->nodeGenes()[42]->aggregationFunction());
        $this->assertEquals([ActivationFunction::class, 'identity'], $obj->nodeGenes()[42]->activationFunction());

        $nodeGenes = $obj->nodeGenes();

        $nodeGenes[21]->setAggregationFunction('array_product');
        $nodeGenes[21]->setActivationFunction([ActivationFunction::class, 'identity']);
        $this->assertEquals('array_product', $nodeGene2->aggregationFunction());
        $this->assertEquals([ActivationFunction::class, 'identity'], $nodeGene2->activationFunction());
    }

    public function testAddConnectGene()
    {
        $obj = $this->getObject();
        $connGene = new \IngeniozIT\Neat\Genotype\ConnectGene(42, 1, 2, 42.42, false);
        $connGene2 = new \IngeniozIT\Neat\Genotype\ConnectGene(21, 2, 3, -42.42, false);
        $connGene3 = new \IngeniozIT\Neat\Genotype\ConnectGene(84, 1, 3, -42.42, false);

        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(1, 1, 'sqrt', 'array_sum'));
        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(2, 1, 'sqrt', 'array_sum'));
        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(3, 1, 'sqrt', 'array_sum'));

        $obj->addConnectGene($connGene);
        $this->assertSame([42 => $connGene], $obj->connectGenes());
        $this->assertEquals(42, $obj->maxConnectInnovation());
        $obj->addConnectGene($connGene2);
        $this->assertSame([21 => $connGene2, 42 => $connGene], $obj->connectGenes());
        $this->assertEquals(42, $obj->maxConnectInnovation());
        $obj->addConnectGene($connGene3);
        $this->assertSame([21 => $connGene2, 42 => $connGene, 84 => $connGene3], $obj->connectGenes());
        $this->assertEquals(84, $obj->maxConnectInnovation());
    }

    public function testAddExistingConnectGene()
    {
        $obj = $this->getObject();
        $connGene = new \IngeniozIT\Neat\Genotype\ConnectGene(42, 1, 2, 42.42, false);
        $connGene2 = new \IngeniozIT\Neat\Genotype\ConnectGene(42, 2, 3, -42.42, false);

        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(1, 1, 'sqrt', 'array_sum'));
        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(2, 1, 'sqrt', 'array_sum'));
        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(3, 1, 'sqrt', 'array_sum'));

        $obj->addConnectGene($connGene);
        $this->expectException(\IngeniozIT\Neat\Exceptions\RuntimeException::class);
        $obj->addConnectGene($connGene2);
    }

    public function testAddConnectGeneNoInNode()
    {
        $obj = $this->getObject();
        $connGene = new \IngeniozIT\Neat\Genotype\ConnectGene(42, 1, 2, 42.42, false);

        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(2, 1, 'sqrt', 'array_sum'));

        $this->expectException(\IngeniozIT\Neat\Exceptions\RuntimeException::class);
        $obj->addConnectGene($connGene);
    }

    public function testAddConnectGeneNoOutNode()
    {
        $obj = $this->getObject();
        $connGene = new \IngeniozIT\Neat\Genotype\ConnectGene(42, 1, 2, 42.42, false);

        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(1, 1, 'sqrt', 'array_sum'));

        $this->expectException(\IngeniozIT\Neat\Exceptions\RuntimeException::class);
        $obj->addConnectGene($connGene);
    }

    public function testConnectGeneModifyGene()
    {
        $obj = $this->getObject();
        $connGene = new \IngeniozIT\Neat\Genotype\ConnectGene(42, 1, 2, 42.42, false);
        $connGene2 = new \IngeniozIT\Neat\Genotype\ConnectGene(21, 2, 3, -42.42, false);

        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(1, 1, 'sqrt', 'array_sum'));
        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(2, 1, 'sqrt', 'array_sum'));
        $obj->addNodeGene(new \IngeniozIT\Neat\Genotype\NodeGene(3, 1, 'sqrt', 'array_sum'));

        $obj->addConnectGene($connGene);
        $obj->addConnectGene($connGene2);

        $connGene->setDisabled(true);
        $connGene->setWeight(-42.42);
        $this->assertTrue($obj->connectGenes()[42]->isDisabled());
        $this->assertEquals(-42.42, $obj->connectGenes()[42]->weight());

        $connGenes = $obj->connectGenes();
        $connGenes[21]->setDisabled(true);
        $connGenes[21]->setWeight(42.42);

        $this->assertTrue($connGene2->isDisabled());
        $this->assertEquals(42.42, $connGene2->weight());
    }

    public function testXorFunction()
    {
        $obj = $this->getObject();

        // Nodes
        for ($i = 1; $i <= 3; ++$i) {
            $obj->addNodeGene(
                new \IngeniozIT\Neat\Genotype\NodeGene(
                    $i,
                    \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR,
                    [ActivationFunction::class, 'binaryStep'],
                    'array_sum'
                )
            );
        }
        $obj->addNodeGene(
            new \IngeniozIT\Neat\Genotype\NodeGene(
                4,
                \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN,
                [ActivationFunction::class, 'binaryStep'],
                'array_sum'
            )
        );
        $obj->addNodeGene(
            new \IngeniozIT\Neat\Genotype\NodeGene(
                5,
                \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT,
                [ActivationFunction::class, 'binaryStep'],
                'array_sum'
            )
        );

        // Connexion genes
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(1, 1, 4, -5, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(2, 2, 4, 10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(3, 2, 5, -10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(4, 3, 4, 10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(5, 3, 5, -10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(6, 4, 5, 15, false));

        $xor = [
            [[1, 0, 0], [0]],
            [[1, 0, 1], [1]],
            [[1, 1, 0], [1]],
            [[1, 1, 1], [0]],
        ];

        foreach ($xor as $xorCase) {
            $this->assertEquals($xorCase[1], $obj->activate($xorCase[0]));
        }

        $this->assertEquals(
            [1, -5, 1, 10, 1, -10, 1, 10, 1, -10, 1, 15],
            $obj->toVector(
                5,
                6,
                [
                    [ActivationFunction::class, 'binaryStep']
                ],
                [
                    'array_sum'
                ]
            )
        );
    }

    public function testXorFunctionWithDisabledNodes()
    {
        $obj = $this->getObject();

        // Nodes
        for ($i = 1; $i <= 3; ++$i) {
            $obj->addNodeGene(
                new \IngeniozIT\Neat\Genotype\NodeGene(
                    $i,
                    \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR,
                    [ActivationFunction::class, 'binaryStep'],
                    'array_sum'
                )
            );
        }
        for ($i = 4; $i <= 5; ++$i) {
            $obj->addNodeGene(
                new \IngeniozIT\Neat\Genotype\NodeGene(
                    $i,
                    \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN,
                    [ActivationFunction::class, 'binaryStep'],
                    'array_sum'
                )
            );
        }
        $obj->addNodeGene(
            new \IngeniozIT\Neat\Genotype\NodeGene(
                6,
                \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT,
                [ActivationFunction::class, 'binaryStep'],
                'array_sum'
            )
        );

        // Connexion genes
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(1, 1, 4, -5, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(2, 1, 5, 15, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(3, 1, 6, -15, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(4, 2, 4, 10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(5, 2, 5, -10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(6, 3, 4, 10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(7, 3, 5, -10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(8, 4, 6, 10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(9, 5, 6, 10, false));

        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(10, 1, 4, 100, true));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(11, 1, 5, -100, true));

        $xor = [
            [[1, 0, 0], [0]],
            [[1, 0, 1], [1]],
            [[1, 1, 0], [1]],
            [[1, 1, 1], [0]],
        ];

        foreach ($xor as $xorCase) {
            $this->assertEquals($xorCase[1], $obj->activate($xorCase[0]));
        }

        $this->assertEquals(
            [1, -5, 1, 15, 1, -15, 1, 10, 1, -10, 1, 10, 1, -10, 1, 10, 1, 10, 0, 100, 0, -100],
            $obj->toVector(
                6,
                11,
                [
                    [ActivationFunction::class, 'binaryStep']
                ],
                [
                    'array_sum'
                ]
            )
        );
    }

    public function testToVectorWeirdNetwork()
    {
        $obj = $this->getObject();

        // Nodes
        for ($i = 1; $i <= 3; ++$i) {
            $obj->addNodeGene(
                new \IngeniozIT\Neat\Genotype\NodeGene(
                    $i,
                    \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR,
                    [ActivationFunction::class, 'identity'],
                    'array_product'
                )
            );
        }
        for ($i = 5; $i <= 6; ++$i) {
            $obj->addNodeGene(
                new \IngeniozIT\Neat\Genotype\NodeGene(
                    $i,
                    \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR,
                    [ActivationFunction::class, 'binaryStep'],
                    'array_sum'
                )
            );
        }
        for ($i = 8; $i <= 9; ++$i) {
            $obj->addNodeGene(
                new \IngeniozIT\Neat\Genotype\NodeGene(
                    $i,
                    \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN,
                    [ActivationFunction::class, 'binaryStep'],
                    'array_sum'
                )
            );
        }
        $obj->addNodeGene(
            new \IngeniozIT\Neat\Genotype\NodeGene(
                11,
                \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT,
                [ActivationFunction::class, 'sigmoid'],
                'array_sum'
            )
        );

        // Connexion genes
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(1, 1, 8, -5, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(2, 1, 9, 15, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(3, 1, 11, -15, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(5, 2, 8, 10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(6, 2, 9, -10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(8, 3, 8, 10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(9, 3, 9, -10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(11, 8, 11, 10, false));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(13, 9, 11, 10, false));

        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(15, 1, 8, 100, true));
        $obj->addConnectGene(new \IngeniozIT\Neat\Genotype\ConnectGene(16, 1, 9, -100, true));

        $this->assertEquals(
            [
            // Nodes
            0, 1, 1, 0, 0,
            0, 1, 1, 0, 0,
            0, 1, 1, 0, 0,

            0, 0, 0, 0, 0,

            1, 0, 0, 1, 0,
            1, 0, 0, 1, 0,

            0, 0, 0, 0, 0,

            1, 0, 0, 1, 0,
            1, 0, 0, 1, 0,

            0, 0, 0, 0, 0,

            1, 0, 0, 0, 1,

            0, 0, 0, 0, 0,

            // Connexions
            1, -5.0,
            1, 15.0,
            1, -15.0,

            0, 0,

            1, 10.0,
            1, -10.0,

            0, 0,

            1, 10.0,
            1, -10.0,

            0, 0,

            1, 10.0,

            0, 0,

            1, 10.0,

            0, 0,

            0, 100.0,
            0, -100.0,

            0, 0
            ], $obj->toVector(
                12,
                17,
                [
                    [ActivationFunction::class, 'identity'],
                    [ActivationFunction::class, 'binaryStep'],
                    [ActivationFunction::class, 'sigmoid']
                ],
                [
                    'array_sum',
                    'array_product'
                ]
            )
        );
    }

    public function testToVectorBadAggregationFunction()
    {
        $obj = $this->getObject();

        // Nodes
        for ($i = 1; $i <= 3; ++$i) {
            $obj->addNodeGene(
                new \IngeniozIT\Neat\Genotype\NodeGene(
                    $i,
                    \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR,
                    [ActivationFunction::class, 'identity'],
                    'array_product'
                )
            );
        }
        for ($i = 5; $i <= 6; ++$i) {
            $obj->addNodeGene(
                new \IngeniozIT\Neat\Genotype\NodeGene(
                    $i,
                    \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR,
                    [ActivationFunction::class, 'binaryStep'],
                    'array_sum'
                )
            );
        }

        $this->expectException(\IngeniozIT\Neat\Exceptions\RuntimeException::class);
        $obj->toVector(
            10,
            10,
            [
                'array_product',
                'max'
            ],
            [
                [ActivationFunction::class, 'identity'],
                [ActivationFunction::class, 'binaryStep'],
                [ActivationFunction::class, 'sigmoid']
            ]
        );
    }

    public function testToVectorBadActivationFunction()
    {
        $obj = $this->getObject();

        // Nodes
        for ($i = 1; $i <= 3; ++$i) {
            $obj->addNodeGene(
                new \IngeniozIT\Neat\Genotype\NodeGene(
                    $i,
                    \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR,
                    [ActivationFunction::class, 'identity'],
                    'array_product'
                )
            );
        }
        for ($i = 5; $i <= 6; ++$i) {
            $obj->addNodeGene(
                new \IngeniozIT\Neat\Genotype\NodeGene(
                    $i,
                    \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR,
                    [ActivationFunction::class, 'binaryStep'],
                    'array_sum'
                )
            );
        }

        $this->expectException(\IngeniozIT\Neat\Exceptions\RuntimeException::class);
        $obj->toVector(
            10,
            10,
            [
                [ActivationFunction::class, 'identity'],
                [ActivationFunction::class, 'sigmoid']
            ],
            [
                'array_product',
                'array_sum'
            ]
        );
    }
}
