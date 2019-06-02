<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents\Interfaces;

interface GenomeFactoryInterface
{
    public function createGenome(array $nodeGenes = [], array $connectGenes = []): GenomeInterface;
}
