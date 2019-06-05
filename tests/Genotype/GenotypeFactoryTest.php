<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Tests\Genotype;

use PHPUnit\Framework\TestCase;
/**
 * @coversDefaultClass \IngeniozIT\Neat\Genotype\GenotypeFactory
 */
class GenotypeFactoryTest extends TestCase
{
    protected function getObject()
    {
        return new \IngeniozIT\Neat\Genotype\GenotypeFactory();
    }

    public function testConstruct()
    {
        $obj = $this->getObject();
        $this->assertTrue($obj instanceof \IngeniozIT\Neat\Genotype\Interfaces\GenotypeFactoryInterface);
    }

    public function testCreateSensorNodeGenotype()
    {
        $obj = $this->getObject();
        $nodeGenotype = $obj->createSensorNodeGenotype(42);

        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface::class, $nodeGenotype);
        $this->assertEquals(42, $nodeGenotype->innovNb());
        $this->assertTrue($nodeGenotype->isSensor());
    }

    public function testCreateOutputNodeGenotype()
    {
        $obj = $this->getObject();
        $nodeGenotype = $obj->createOutputNodeGenotype(42);

        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface::class, $nodeGenotype);
        $this->assertEquals(42, $nodeGenotype->innovNb());
        $this->assertTrue($nodeGenotype->isOutput());
    }

    public function testCreateHiddenNodeGenotype()
    {
        $obj = $this->getObject();
        $nodeGenotype = $obj->createHiddenNodeGenotype(42);

        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface::class, $nodeGenotype);
        $this->assertEquals(42, $nodeGenotype->innovNb());
        $this->assertTrue($nodeGenotype->isHidden());
    }

    public function testCreateNodeGenotype()
    {
        $obj = $this->getObject();

        $nodeGenotype = $obj->createNodeGenotype(42, \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR);
        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface::class, $nodeGenotype);
        $this->assertEquals(42, $nodeGenotype->innovNb());
        $this->assertTrue($nodeGenotype->isSensor());

        $nodeGenotype = $obj->createNodeGenotype(42, \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT);
        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface::class, $nodeGenotype);
        $this->assertEquals(42, $nodeGenotype->innovNb());
        $this->assertTrue($nodeGenotype->isOutput());

        $nodeGenotype = $obj->createNodeGenotype(42, \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN);
        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface::class, $nodeGenotype);
        $this->assertEquals(42, $nodeGenotype->innovNb());
        $this->assertTrue($nodeGenotype->isHidden());
    }

    public function testCreateNodeGeneFromNodeGenotype()
    {
        $obj = $this->getObject();

        $nodeGenotype = $obj->createSensorNodeGenotype(21);
        $nodeGene = $obj->createNodeGeneFromNodeGenotype($nodeGenotype, 'sqrt', 'array_sum');
        $this->assertEquals(21, $nodeGene->innovNb());
        $this->assertTrue($nodeGene->isSensor());
        $this->assertSame('sqrt', $nodeGene->activationFunction());
        $this->assertSame('array_sum', $nodeGene->aggregationFunction());

        $nodeGenotype = $obj->createOutputNodeGenotype(42);
        $nodeGene = $obj->createNodeGeneFromNodeGenotype($nodeGenotype, 'sqrt', 'array_product');
        $this->assertEquals(42, $nodeGene->innovNb());
        $this->assertTrue($nodeGene->isOutput());
        $this->assertSame('sqrt', $nodeGene->activationFunction());
        $this->assertSame('array_product', $nodeGene->aggregationFunction());


        $nodeGenotype = $obj->createHiddenNodeGenotype(84);
        $nodeGene = $obj->createNodeGeneFromNodeGenotype($nodeGenotype, 'sqrt', 'array_sum');
        $this->assertEquals(84, $nodeGene->innovNb());
        $this->assertTrue($nodeGene->isHidden());
        $this->assertSame('sqrt', $nodeGene->activationFunction());
        $this->assertSame('array_sum', $nodeGene->aggregationFunction());
    }

    public function testCreateNodeGene()
    {
        $obj = $this->getObject();

        $nodeGene = $obj->createNodeGene(21, \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR, 'sqrt', 'array_sum');
        $this->assertEquals(21, $nodeGene->innovNb());
        $this->assertTrue($nodeGene->isSensor());
        $this->assertSame('sqrt', $nodeGene->activationFunction());
        $this->assertSame('array_sum', $nodeGene->aggregationFunction());

        $nodeGene = $obj->createNodeGene(42, \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT, 'sqrt', 'array_product');
        $this->assertEquals(42, $nodeGene->innovNb());
        $this->assertTrue($nodeGene->isOutput());
        $this->assertSame('sqrt', $nodeGene->activationFunction());
        $this->assertSame('array_product', $nodeGene->aggregationFunction());


        $nodeGene = $obj->createNodeGene(84, \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN, 'sqrt', 'array_sum'); 
        $this->assertEquals(84, $nodeGene->innovNb());
        $this->assertTrue($nodeGene->isHidden());
        $this->assertSame('sqrt', $nodeGene->activationFunction());
        $this->assertSame('array_sum', $nodeGene->aggregationFunction());
    }
}
