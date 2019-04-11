<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\NEAT\Genome;
use IngeniozIT\Math\ActivationFunction;

class GenomeTest extends TestCase
{
	public function testBadActivationFunction()
	{
        $this->expectException(\Exception::class);
        $genome = new Genome([], [
			0 => 'array_sum',
		]);
        $genome->addInputNode(1, 0, 0);
	}

	public function testBadAggregationFunction()
	{
        $this->expectException(\Exception::class);
        $genome = new Genome([
			0 => [ActivationFunction::class, 'identity'],
		], []);
        $genome->addInputNode(1, 0, 0);
	}

	public function testHasConnexion()
	{
        $genome = new Genome([
			0 => [ActivationFunction::class, 'identity'],
		], [
			0 => 'array_sum',
		]);
        $genome->addInputNode(1, 0, 0);
        $genome->addOutputNode(2, 0, 0);
		$genome->addConnexionGene(42, 1, 2, 1);

		$this->assertTrue($genome->hasConnexion(42));
		$this->assertFalse($genome->hasConnexion(1));
	}

	public function testCheckConnexion()
	{
        $genome = new Genome([
			0 => [ActivationFunction::class, 'identity'],
		], [
			0 => 'array_sum',
		]);

        $this->expectException(\Exception::class);
        $genome->checkConnexion(42);
	}

	public function testHasNode()
	{
        $genome = new Genome([
			0 => [ActivationFunction::class, 'identity'],
		], [
			0 => 'array_sum',
		]);
        $genome->addInputNode(42, 0, 0);

        $this->assertTrue($genome->hasNode(42));
        $this->assertFalse($genome->hasNode(1));
	}

	public function testCheckNode()
	{
        $genome = new Genome([
			0 => [ActivationFunction::class, 'identity'],
		], [
			0 => 'array_sum',
		]);

        $this->expectException(\Exception::class);
        $genome->checkNode(42);
	}

	public function testSnakeNetwork()
	{
		$genome = new Genome([
			0 => [ActivationFunction::class, 'identity'],
		], [
			0 => 'array_sum',
		]);

		$genome->addInputNode(1, 0, 0);
		$genome->addHiddenNode(2, 0, 0);
		$genome->addHiddenNode(3, 0, 0);
		$genome->addHiddenNode(4, 0, 0);
		$genome->addOutputNode(5, 0, 0);

		$genome->addConnexionGene(1, 1, 2, 1);
		$genome->addConnexionGene(2, 2, 3, 1);
		$genome->addConnexionGene(3, 3, 4, 1);
		$genome->addConnexionGene(4, 4, 5, 1);

		$this->assertEquals([1], $genome->activate([1]));

		return $genome;
	}

    /**
     * @depends testSnakeNetwork
     */
	public function testDisabledNode(Genome $genome)
	{
		$genome->disableNode(2);
		$this->assertEquals([0], $genome->activate([1]));

		$genome->enableNode(2);
		$this->assertEquals([1], $genome->activate([1]));

		$genome->toggleNode(1);
		$this->assertEquals([0], $genome->activate([1]));
	}

	public function testOr()
	{
		$orCases = [
			[0, 0, 0],
			[0, 1, 1],
			[1, 0, 1],
			[1, 1, 1],
		];

		$genome = new Genome([
			0 => [ActivationFunction::class, 'identity'],
			1 => [ActivationFunction::class, 'binaryStep'],
		], [
			0 => 'array_sum',
		]);

		// OR

		$genome->addInputNode(1, 0, 0);
		$genome->addInputNode(2, 0, 0);
		$genome->addOutputNode(3, 1, 0);

		$genome->addConnexionGene(1, 1, 3, 1);
		$genome->addConnexionGene(1, 2, 3, 1);

		foreach ($orCases as $case) {
			$this->assertEquals($genome->activate([$case[0], $case[1]])[0], $case[2]);
		}
	}

	public function testAnd()
	{
		$andCases = [
			[0, 0, 0],
			[0, 1, 0],
			[1, 0, 0],
			[1, 1, 1],
		];

		$genome = new Genome([
			0 => [ActivationFunction::class, 'binaryStep'],
		], [
			0 => 'array_sum',
		]);

		$genome->addInputNode(1, 0, 0);
		$genome->addInputNode(2, 0, 0);
		$genome->addInputNode(3, 0, 0);
		$genome->addOutputNode(4, 0, 0);

		$genome->addConnexionGene(1, 1, 4, 1);
		$genome->addConnexionGene(2, 2, 4, 1);
		$genome->addConnexionGene(3, 3, 4, -1.5);

		foreach ($andCases as $case) {
			$this->assertEquals($case[2], $genome->activate([$case[0], $case[1], 1])[0]);
		}

		return $genome;
	}

    /**
     * @depends testAnd
     */
	public function testNand(Genome $genome)
	{
		$nandCases = [
			[0, 0, 1],
			[0, 1, 1],
			[1, 0, 1],
			[1, 1, 0],
		];

		$genome->setConnexionWeight(1, -$genome->getConnexionWeight(1));
		$genome->setConnexionWeight(2, -$genome->getConnexionWeight(2));
		$genome->setConnexionWeight(3, -$genome->getConnexionWeight(3));

		foreach ($nandCases as $case) {
			$this->assertEquals($case[2], $genome->activate([$case[0], $case[1], 1])[0]);
		}
	}

	public function testXor()
	{
		$xorCases = [
			[0, 0, 0],
			[0, 1, 1],
			[1, 0, 1],
			[1, 1, 0],
		];

		$genome = new Genome([
			0 => [ActivationFunction::class, 'binaryStep'],
		], [
			0 => 'array_sum',
		]);

		$genome->addInputNode(1, 0, 0);
		$genome->addInputNode(2, 0, 0);
		$genome->addInputNode(3, 0, 0);
		$genome->addOutputNode(4, 0, 0);
		$genome->addHiddenNode(5, 0, 0);
		$genome->addHiddenNode(6, 0, 0);

		// OR
		$genome->addConnexionGene(1, 1, 5, 1);
		$genome->addConnexionGene(2, 2, 5, 1);

		// NAND
		$genome->addConnexionGene(3, 1, 6, -1);
		$genome->addConnexionGene(4, 2, 6, -1);
		$genome->addConnexionGene(5, 3, 6, 1.5);

		// OR AND NAND => XOR
		$genome->addConnexionGene(6, 3, 4, -1.5);
		$genome->addConnexionGene(7, 5, 4, 1);
		$genome->addConnexionGene(8, 6, 4, 1);

		foreach ($xorCases as $case) {
			$this->assertEquals($case[2], $genome->activate([$case[0], $case[1], 1])[0]);
		}
	}
}

