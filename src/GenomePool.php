<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

use IngeniozIT\NEAT\Interfaces\GenomePoolInterface;
use IngeniozIT\NEAT\Interfaces\GenePoolInterface;
use IngeniozIT\NEAT\Interfaces\GenomeInterface;

class GenomePool implements GenomePoolInterface
{
    protected $genePool;
    protected $genomes = [];
    protected $species = [];

    public function __construct(GenePoolInterface $genePool)
    {
        $this->genePool = $genePool;
    }

    public function &getGenePool(): GenePoolInterface
    {
        return $this->genePool;
    }

    public function addGenome(GenomeInterface &$genome): GenomePoolInterface
    {
        $this->genomes[] = $genome;

        return $this;
    }

    public function removeGenome(int $genomeId): GenomePoolInterface
    {
        $speciesId = $this->genomes[$genomeId]->getSpecies();
        if (isset($this->species[$speciesId][$genomeId])) {
            unset($this->species[$this->genomes[$genomeId]->getSpecies()][$genomeId]);
        }
        unset($this->genomes[$genomeId]);

        return $this;
    }

    public function &getGenomes(): array
    {
        return $this->genomes;
    }

    public function getVectors(): array
    {
        return array_map([$this, 'genomeToVector'], $this->getGenomes());
    }

    protected function genomeToVector(GenomeInterface $genome): array
    {
        return $genome->getVector();
    }

    public function getSpecies(): array
    {
        return $this->species;
    }

    public function assignGenomesToSpecies(array $genomesId, int $speciesId): GenomePoolInterface
    {
        foreach ($genomesId as $genomeId) {
            $this->genomes[$genomeId]->setSpecies($speciesId);
            $this->species[$speciesId][] = $genomeId;
        }

        return $this;
    }

    public function resetSpecies(): GenomePoolInterface
    {
        foreach ($this->genomes as &$genome) {
            $genome->setSpecies(null);
        }
        $this->species = [];

        return $this;
    }
}
