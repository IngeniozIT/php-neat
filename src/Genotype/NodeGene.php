<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface;

class NodeGene extends NodeGenotype implements NodeGeneInterface
{
    protected $aggregationFunction;
    protected $activationFunction;

    public function __construct(int $innovId, int $type, callable $aggregationFunction, callable $activationFunction)
    {
        parent::__construct($innovId, $type);

        $this->aggregationFunction = $aggregationFunction;
        $this->activationFunction = $activationFunction;
    }

    public function aggregationFunction(): callable
    {
        return $this->aggregationFunction;
    }

    public function setAggregationFunction(callable $aggrFunction): void
    {
        $this->aggregationFunction = $aggrFunction;
    }

    public function activationFunction(): callable
    {
        return $this->activationFunction;
    }

    public function setActivationFunction(callable $actFunction): void
    {
        $this->activationFunction = $actFunction;
    }
}
