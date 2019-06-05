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

    public function testCreateNodeGenotype()
    {
        $obj = $this->getObject();

        $nodeGenotype = $obj->createNodeGenotype(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR, 42);
        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface::class, $nodeGenotype);
        $this->assertEquals(42, $nodeGenotype->innovNb());
        $this->assertTrue($nodeGenotype->isSensor());

        $nodeGenotype = $obj->createNodeGenotype(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT, 42);
        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface::class, $nodeGenotype);
        $this->assertEquals(42, $nodeGenotype->innovNb());
        $this->assertTrue($nodeGenotype->isOutput());

        $nodeGenotype = $obj->createNodeGenotype(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN, 42);
        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface::class, $nodeGenotype);
        $this->assertEquals(42, $nodeGenotype->innovNb());
        $this->assertTrue($nodeGenotype->isHidden());
    }

    public function testCreateNodeGenotypeNoInnovNb()
    {
        $obj = $this->getObject();
        $nodeGenotype = $obj->createNodeGenotype(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR);
        $nodeGenotype2 = $obj->createNodeGenotype(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR);

        $this->assertNotEquals($nodeGenotype->innovNb(), $nodeGenotype2->innovNb());
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

    public function testCreateNodeGene()
    {
        $obj = $this->getObject();

        $nodeGene = $obj->createNodeGene(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR, 'sqrt', 'array_sum', 21);
        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface::class, $nodeGene);
        $this->assertEquals(21, $nodeGene->innovNb());
        $this->assertTrue($nodeGene->isSensor());
        $this->assertSame('sqrt', $nodeGene->activationFunction());
        $this->assertSame('array_sum', $nodeGene->aggregationFunction());

        $nodeGene = $obj->createNodeGene(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT, 'sqrt', 'array_product', 42);
        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface::class, $nodeGene);
        $this->assertEquals(42, $nodeGene->innovNb());
        $this->assertTrue($nodeGene->isOutput());
        $this->assertSame('sqrt', $nodeGene->activationFunction());
        $this->assertSame('array_product', $nodeGene->aggregationFunction());


        $nodeGene = $obj->createNodeGene(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN, 'sqrt', 'array_sum', 84);
        $this->assertInstanceOf(\IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface::class, $nodeGene);
        $this->assertEquals(84, $nodeGene->innovNb());
        $this->assertTrue($nodeGene->isHidden());
        $this->assertSame('sqrt', $nodeGene->activationFunction());
        $this->assertSame('array_sum', $nodeGene->aggregationFunction());
    }

    public function testCreateNodeGeneNoInnovNb()
    {
        $obj = $this->getObject();
        $nodeGene = $obj->createNodeGene(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN, 'sqrt', 'array_sum');
        $nodeGene2 = $obj->createNodeGene(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN, 'sqrt', 'array_sum');

        $this->assertNotEquals($nodeGene->innovNb(), $nodeGene2->innovNb());
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
}
