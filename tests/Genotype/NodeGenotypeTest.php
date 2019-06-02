<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Tests\Genotype;

/**
 * @coversDefaultClass \IngeniozIT\Neat\Genotype\NodeGenotype
 */
class NodeGenotypeTest extends InnovationGenotypeTest
{
    protected function getObject($innovId, $type = 1)
    {
        return new \IngeniozIT\Neat\Genotype\NodeGenotype($innovId, $type);
    }

    public function testConstruct()
    {
        $obj = $this->getObject(42);
        $this->assertTrue($obj instanceof \IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface);
    }

    public function testWrongType()
    {
        $this->expectException(\IngeniozIT\Neat\Exceptions\InvalidArgumentException::class);
        $obj = $this->getObject(42, 5);
    }

    public function testSensorNode()
    {
        $obj = $this->getObject(42, \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR);
        $this->assertEquals(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_SENSOR, $obj->type());
        $this->assertTrue($obj->isSensor());
        $this->assertFalse($obj->isOutput());
        $this->assertFalse($obj->isHidden());
    }

    public function testOutputNode()
    {
        $obj = $this->getObject(42, \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT);
        $this->assertEquals(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_OUTPUT, $obj->type());
        $this->assertFalse($obj->isSensor());
        $this->assertTrue($obj->isOutput());
        $this->assertFalse($obj->isHidden());
    }

    public function testHiddenNode()
    {
        $obj = $this->getObject(42, \IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN);
        $this->assertEquals(\IngeniozIT\Neat\Genotype\NodeGenotype::TYPE_HIDDEN, $obj->type());
        $this->assertFalse($obj->isSensor());
        $this->assertFalse($obj->isOutput());
        $this->assertTrue($obj->isHidden());
    }
}
