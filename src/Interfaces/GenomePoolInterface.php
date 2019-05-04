<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Interfaces;

interface GenomePoolInterface
{
    public function __construct(GenePoolInterface $genePool);

    public function &getGenePool(): GenePoolInterface;

    public function addGenome(GenomeInterface &$genome): GenomePoolInterface;

    public function &getGenomes(): array;

    public function getVectors(): array;

    public function getSpecies(): array;

    public function assignGenomesToSpecies(array $genomesId, int $speciesId): GenomePoolInterface;

    public function resetSpecies(): GenomePoolInterface;
}
