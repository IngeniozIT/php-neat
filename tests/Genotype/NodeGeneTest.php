<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Tests\Genotype;

/**
 * @coversDefaultClass \IngeniozIT\Neat\Genotype\NodeGene
 */
class NodeGeneTest extends NodeGenotypeTest
{
    protected function getObject($innovNb, $type = 1, $activationFunction = [self::class, 'dummyActivationFunction'], $aggregationFunction = [self::class, 'dummyAggregationFunction'])
    {
        return new \IngeniozIT\Neat\Genotype\NodeGene(
            $innovNb,
            $type,
            $activationFunction,
            $aggregationFunction
        );
    }

    public function testConstruct()
    {
        $obj = $this->getObject(42);
        $this->assertTrue($obj instanceof \IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface);
    }

    public function testAggregationAndActivationFunctions()
    {
        $activationFunction = function (array $a) {
            return $a;
        };
        $aggregationFunction = function (array $a) {
            return sum($a);
        };
        $obj = $this->getObject(42, 1, $activationFunction, $aggregationFunction);
        $this->assertSame($aggregationFunction, $obj->aggregationFunction());
        $this->assertSame($activationFunction, $obj->activationFunction());
    }

    public static function dummyAggregationFunction()
    {

    }

    public static function dummyActivationFunction()
    {

    }
}
