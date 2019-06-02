<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface NodeGeneInterface extends NodeGenotypeInterface
{
    public function aggregationFunction(): callable;
    public function setAggregationFunction(callable $aggrFunction): void;
    public function activationFunction(): callable;
    public function setActivationFunction(callable $actFunction): void;
}
