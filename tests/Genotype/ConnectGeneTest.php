<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Tests\Genotype;

/**
 * @coversDefaultClass \IngeniozIT\Neat\Genotype\ConnectGene
 */
class ConnectGeneTest extends ConnectGenotypeTest
{
    protected function getObject($innovId, $inId = 1, $outId = 1, $weight = 0.0, $disabled = false)
    {
        return new \IngeniozIT\Neat\Genotype\ConnectGene($innovId, $inId, $outId, $weight, $disabled);
    }

    public function testConstruct()
    {
        $obj = $this->getObject(42);
        $this->assertTrue($obj instanceof \IngeniozIT\Neat\Genotype\Interfaces\ConnectGeneInterface);
    }

    public function testWeightAndDisabled()
    {
        $obj = $this->getObject(1, 1, 1, 42.42, false);
        $this->assertEquals(42.42, $obj->weight());
        $this->assertFalse($obj->isDisabled());

        $obj = $this->getObject(1, 1, 1, -42.42, true);
        $this->assertEquals(-42.42, $obj->weight());
        $this->assertTrue($obj->isDisabled());
    }

    public function testSetWeightAndSetDisabled()
    {
        $obj = $this->getObject(1, 1, 1, 42.42, false);
        $obj->setWeight(-42.42);
        $obj->setDisabled(true);
        $this->assertEquals(-42.42, $obj->weight());
        $this->assertTrue($obj->isDisabled());

        $obj = $this->getObject(1, 1, 1, -42.42, true);
        $obj->setWeight(42.42);
        $obj->setDisabled(false);
        $this->assertEquals(42.42, $obj->weight());
        $this->assertFalse($obj->isDisabled());
    }
}
