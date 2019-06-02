<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents\Interfaces;

use IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface;
use IngeniozIT\Neat\Genotype\Interfaces\ConnectGeneInterface;

interface GenomeInterface
{
    public function nodeGenes(): array;
    public function connectGenes(): array;
    public function addNodeGene(NodeGeneInterface $nodeGene): void;
    public function addConnectGene(ConnectGeneInterface $connectGene): void;
    public function activate(array $inputValues): array;
    public function toVector(int $nodeInnovation, int $connInnovation, array $aggregationFunctions, array $activationFunctions): array;
}
