<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Tests\Genotype;

/**
 * @coversDefaultClass \IngeniozIT\Neat\Genotype\ConnectGenotype
 */
class ConnectGenotypeTest extends InnovationGenotypeTest
{
    protected function getObject($innovId, $inId = 1, $outId = 1)
    {
        return new \IngeniozIT\Neat\Genotype\ConnectGenotype($innovId, $inId, $outId);
    }

    public function testConstruct()
    {
        $obj = $this->getObject(42);
        $this->assertTrue($obj instanceof \IngeniozIT\Neat\Genotype\Interfaces\ConnectGenotypeInterface);
    }

    public function testInIdOutId()
    {
        $obj = $this->getObject(1, 42, 84);
        $this->assertEquals(42, $obj->inId());
        $this->assertEquals(84, $obj->outId());

        $obj = $this->getObject(1, 84, 42);
        $this->assertEquals(84, $obj->inId());
        $this->assertEquals(42, $obj->outId());
    }
}
