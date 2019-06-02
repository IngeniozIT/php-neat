<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Tests\Genotype;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \IngeniozIT\Neat\Genotype\InnovationGenotype
 */
class InnovationGenotypeTest extends TestCase
{
    protected function getObject($innovId)
    {
        return $this->getMockForAbstractClass(
            \IngeniozIT\Neat\Genotype\InnovationGenotype::class,
            [$innovId]
        );
    }

    public function testConstruct()
    {
        $obj = $this->getObject(42);
        $this->assertTrue($obj instanceof \IngeniozIT\Neat\Genotype\Interfaces\InnovationGenotypeInterface);
    }

    public function testInnovId()
    {
        $obj = $this->getObject(42);
        $this->assertEquals(42, $obj->innovId());

        $obj = $this->getObject(84);
        $this->assertEquals(84, $obj->innovId());
    }
}
