<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Tests\Agents;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \IngeniozIT\Neat\Agents\Agent
 */
class AgentTest extends TestCase
{
    protected function getObject()
    {
        return new \IngeniozIT\Neat\Agents\Agent();
    }

    public function testConstruct()
    {
        $obj = $this->getObject();
        $this->assertTrue($obj instanceof \IngeniozIT\Neat\Agents\Agent);
    }

    public function testFitness()
    {
        $obj = $this->getObject();
        $this->assertNull($obj->fitness());

        $obj->setFitness(42.42);
        $this->assertEquals(42.42, $obj->fitness());

        $obj->setFitness(-42.42);
        $this->assertEquals(-42.42, $obj->fitness());

        $obj->setFitness(null);
        $this->assertNull($obj->fitness());
    }
}
