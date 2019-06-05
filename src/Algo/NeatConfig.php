<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Algo;

use IngeniozIT\Neat\Algo\Interfaces\NeatConfigInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Threshold\Interfaces\ThresholdInterface;
use IngeniozIT\Neat\Implementation\Interfaces\SelectionInterface;
use IngeniozIT\Neat\Implementation\Interfaces\MatingInterface;
use IngeniozIT\Neat\Implementation\Interfaces\SpeciationInterface;

class NeatConfig implements NeatConfigInterface
{
    protected $pool;
    protected $threshold;
    protected $fitnessFunction;
    protected $selectionFunction;
    protected $matingFunction;
    protected $speciationFunction;

    public function __construct(
        PoolInterface $pool,
        ThresholdInterface $threshold,
        callable $fitnessFunction,
        SelectionInterface $selectionFunction,
        MatingInterface $matingFunction,
        SpeciationInterface $speciationFunction
    )
    {
        $this->pool = $pool;
        $this->threshold = $threshold;
        $this->fitnessFunction = $fitnessFunction;
        $this->selectionFunction = $selectionFunction;
        $this->matingFunction = $matingFunction;
        $this->speciationFunction = $speciationFunction;
    }

    public function pool(): PoolInterface
    {
        return $this->pool;
    }

    public function threshold(): ThresholdInterface
    {
        return $this->threshold;
    }

    public function fitnessFunction(): callable
    {
        return $this->fitnessFunction;
    }

    public function speciationFunction(): callable
    {
        return $this->speciationFunction;
    }

    public function selectionFunction(): callable
    {
        return $this->selectionFunction;
    }

    public function matingFunction(): callable
    {
        return $this->matingFunction;
    }
}
