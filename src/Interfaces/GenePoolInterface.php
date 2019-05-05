<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Interfaces;

interface GenePoolInterface
{
    const NODE_INPUT = 1;
    const NODE_OUTPUT = 2;
    const NODE_HIDDEN = 4;

    public function addInputGene(): void;

    public function inputGenes(): array;

    public function addOutputGene(): void;

    public function outputGenes(): array;

    public function addHiddenGene(): void;

    public function hiddenGenes(): array;

    public function nodeGenes(): array;

    public function nodeGeneExists(int $nodeId, int $nodeType = null): bool;

    public function addConnexionGene(int $inId, int $outId): void;

    public function connexionGeneId(int $inId, int $outId): int;

    public function connexionGenes(): array;
}
