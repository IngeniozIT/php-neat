<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\NEAT;
use IngeniozIT\NEAT\Interfaces\NeatConfigInterface;
use IngeniozIT\NEAT\Genome;
use IngeniozIT\NEAT\GenePool;
use IngeniozIT\NEAT\GenomePool;
use IngeniozIT\Math\ActivationFunction;

class NeatConfigTest extends TestCase
{
    protected $classname = NEAT::class;

    public function testConstruct()
    {
        $neat = new $this->classname();

        $this->assertInstanceOf($this->classname, $neat);

        return $neat;
    }

    /**
     * @depends testConstruct
     */
    public function testNbInputs(NeatConfigInterface $neat)
    {
        $neat->nbInputs(42);
        $this->assertEquals($neat->getNbInputs(), 42);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->nbInputs(0);
    }

    /**
     * @depends testConstruct
     */
    public function testNbOuputs(NeatConfigInterface $neat)
    {
        $neat->nbOutputs(42);
        $this->assertEquals($neat->getNbOutputs(), 42);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->nbOutputs(0);
    }

    /**
     * @depends testConstruct
     */
    public function testPopulationSize(NeatConfigInterface $neat)
    {
        $neat->populationSize(42);
        $this->assertEquals($neat->getPopulationSize(), 42);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->populationSize(0);
    }

    public function testAutoPopulationSize()
    {
        $neat = new $this->classname();
        $neat->nbInputs(20);
        $neat->nbOutputs(10);
        $this->assertEquals($neat->getPopulationSize(), 400);

        $neat = new $this->classname();
        $neat->nbOutputs(10);
        $neat->nbInputs(20);
        $this->assertEquals($neat->getPopulationSize(), 400);
    }

    /**
     * @depends testConstruct
     */
    public function testMaxGenerations(NeatConfigInterface $neat)
    {
        $neat->maxGenerations(42);
        $this->assertEquals($neat->getMaxGenerations(), 42);
        $neat->maxGenerations(0);
        $this->assertEquals($neat->getMaxGenerations(), 0);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->maxGenerations(-1);
    }

    /**
     * @depends testConstruct
     */
    public function testFitnessCriterion(NeatConfigInterface $neat)
    {
        $neat->fitnessCriterion([$this, 'sampleFitnessCriterion']);
        $this->assertEquals($neat->getFitnessCriterion(), [$this, 'sampleFitnessCriterion']);
        /**
         * @todo check if callable has right parameters
         */
    }

    /**
     * @depends testConstruct
     */
    public function testFitnessThreshold(NeatConfigInterface $neat)
    {
        $neat->fitnessThreshold(42.42);
        $this->assertEquals($neat->getFitnessThreshold(), 42.42);
    }

    /**
     * @depends testConstruct
     */
    public function testFitnessFunction(NeatConfigInterface $neat)
    {
        $neat->fitnessFunction([$this, 'sampleFitnessFunction']);
        $this->assertSame($neat->getFitnessFunction(), [$this, 'sampleFitnessFunction']);
        /**
         * @todo check if callable has right parameters
         */
    }

    /**
     * @depends testConstruct
     */
    public function testInitializationMethod(NeatConfigInterface $neat)
    {
        $neat->initializationMethod([$this, 'sampleInitializationMethod']);
        $this->assertSame($neat->getInitializationMethod(), [$this, 'sampleInitializationMethod']);
        /**
         * @todo check if callable has right parameters
         */
    }

    /**
     * @depends testConstruct
     */
    public function testActivationFunctions(NeatConfigInterface $neat)
    {
        $functions = [
            [ActivationFunction::class, 'sigmoid'],
            [ActivationFunction::class, 'identity'],
        ];
        $neat->activationFunctions($functions);
        $this->assertSame($neat->getActivationFunctions(), $functions);
        /**
         * @todo check if callable has right parameters
         */
    }

    /**
     * @depends testConstruct
     */
    public function testDefaultActivationFunction(NeatConfigInterface $neat)
    {
        $functions = [
            [ActivationFunction::class, 'sigmoid'],
            [ActivationFunction::class, 'identity'],
        ];
        $neat->activationFunctions($functions);
        $neat->defaultActivationFunction(1);
        $this->assertSame($neat->getDefaultActivationFunction(), 1);
        /**
         * @todo check if callable has right parameters
         */
    }

    /**
     * @depends testConstruct
     */
    public function testAggregationFunction(NeatConfigInterface $neat)
    {
        $functions = [
            'array_sum',
            'array_product',
        ];
        $neat->aggregationFunctions($functions);
        $this->assertSame($neat->getAggregationFunctions(), $functions);
        /**
         * @todo check if callable has right parameters
         */
    }

    /**
     * @depends testConstruct
     */
    public function testDefaultAggregationFunction(NeatConfigInterface $neat)
    {
        $functions = [
            'array_sum',
            'array_product',
        ];
        $neat->aggregationFunctions($functions);
        $neat->defaultAggregationFunction(1);
        $this->assertSame($neat->getDefaultAggregationFunction(), 1);
        /**
         * @todo check if callable has right parameters
         */
    }

    /**
     * @depends testConstruct
     */
    public function testWeightInitializationMean(NeatConfigInterface $neat)
    {
        $neat->weightInitializationMean(42.42);
        $this->assertEquals($neat->getWeightInitializationMean(), 42.42);
    }

    /**
     * @depends testConstruct
     */
    public function testWeightInitializationStdev(NeatConfigInterface $neat)
    {
        $neat->weightInitializationStdev(42.42);
        $this->assertEquals($neat->getWeightInitializationStdev(), 42.42);
    }

    /**
     * @depends testConstruct
     */
    public function testWeightMinValue(NeatConfigInterface $neat)
    {
        $neat->weightMinValue(-42.42);
        $this->assertEquals($neat->getWeightMinValue(), -42.42);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->weightMinValue($neat->getWeightMaxValue() + 0.1);
    }

    /**
     * @depends testConstruct
     */
    public function testWeightMaxValue(NeatConfigInterface $neat)
    {
        $neat->weightMaxValue(42.42);
        $this->assertEquals($neat->getWeightMaxValue(), 42.42);

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->weightMaxValue($neat->getWeightMinValue() - 0.1);
    }

    /**
     * @depends testConstruct
     */
    public function testGenomeClass(NeatConfigInterface $neat)
    {
        $neat->genomeClass(Genome::class);
        $this->assertEquals($neat->getGenomeClass(), Genome::class);
        /**
         * @todo check if callable has right parameters
         */

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->genomeClass('NullClass');
    }

    /**
     * @depends testConstruct
     */
    public function testGenomePoolClass(NeatConfigInterface $neat)
    {
        $neat->genomePoolClass(GenomePool::class);
        $this->assertEquals($neat->getGenomePoolClass(), GenomePool::class);
        /**
         * @todo check if callable has right parameters
         */

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->genomePoolClass('NullClass');
    }

    /**
     * @depends testConstruct
     */
    public function testGenePoolClass(NeatConfigInterface $neat)
    {
        $neat->genePoolClass(GenePool::class);
        $this->assertEquals($neat->getGenePoolClass(), GenePool::class);
        /**
         * @todo check if callable has right parameters
         */

        $this->expectException(\IngeniozIT\NEAT\Exceptions\NeatConfigException::class);
        $neat->genePoolClass('NullClass');
    }

    /**
     * @depends testConstruct
     */
    public function testValidateConfig(NeatConfigInterface $neat)
    {
        $neat
            ->nbInputs(3)
            ->nbOutputs(1)
            ->populationSize(50)
            ->maxGenerations(100);

        $neat->validateConfig();
        $this->assertTrue(true);
    }

    public function sampleFitnessFunction(array &$genomes): void
    {
    }

    public function sampleFitnessCriterion()
    {
    }

    public function sampleInitializationMethod(NEAT &$neat)
    {
    }
}
