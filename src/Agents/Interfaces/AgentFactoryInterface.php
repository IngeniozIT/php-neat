<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents\Interfaces;

interface AgentFactoryInterface
{
    public function createGenome(array $nodeGenes = [], array $connectGenes = []): GenomeInterface;
    public function createGenomeFromParents(GenomeInterface $parent1, GenomeInterface $parent2): GenomeInterface;
}
