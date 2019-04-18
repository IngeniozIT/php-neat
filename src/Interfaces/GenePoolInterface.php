<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Interfaces;

interface GenePoolInterface
{
    const NODE_INPUT = 1;
    const NODE_OUTPUT = 2;
    const NODE_HIDDEN = 4;

    public function addInputGene(): void;

    public function getInputGenes(): array;

    public function addOutputGene(): void;

    public function getOutputGenes(): array;

    public function addHiddenGene(): void;

    public function getHiddenGenes(): array;

    public function getNodeGenes(): array;

    public function nodeGeneExists(int $nodeId, int $nodeType = null): bool;

    public function addConnexionGene(int $inId, int $outId): void;

    public function getConnexionGeneId(int $inId, int $outId): int;

    public function getConnexionGenes(): array;
}
