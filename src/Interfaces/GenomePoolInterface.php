<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Interfaces;

interface GenomePoolInterface
{
    public function __construct(GenePoolInterface $genePool);

    public function &genePool(): GenePoolInterface;

    public function addGenome(GenomeInterface &$genome): GenomePoolInterface;

    public function &genomes(): array;

    public function vectors(): array;

    public function species(): array;

    public function assignGenomesToSpecies(array $genomesId, ?int $speciesId): GenomePoolInterface;

    public function resetSpecies(): GenomePoolInterface;
}
