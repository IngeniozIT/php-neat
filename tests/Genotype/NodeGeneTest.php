<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Tests\Genotype;

/**
 * @coversDefaultClass \IngeniozIT\Neat\Genotype\NodeGene
 */
class NodeGeneTest extends NodeGenotypeTest
{
    protected function getObject($innovNb, $type = 1, $aggregationFunction = [self::class, 'dummyAggregationFunction'], $activationFunction = [self::class, 'dummyActivationFunction'])
    {
        return new \IngeniozIT\Neat\Genotype\NodeGene(
            $innovNb,
            $type,
            $aggregationFunction,
            $activationFunction
        );
    }

    public function testConstruct()
    {
        $obj = $this->getObject(42);
        $this->assertTrue($obj instanceof \IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface);
    }

    public function testAggregationAndActivationFunctions()
    {
        $aggregationFunction = function (array $a) {
            return sum($a);
        };
        $activationFunction = function (array $a) {
            return $a;
        };
        $obj = $this->getObject(42, 1, $aggregationFunction, $activationFunction);
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
