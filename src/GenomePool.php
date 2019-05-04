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
        $this->assignGenomesToSpecies([$genomeId], null);
        unset($this->genomes[$genomeId]);

        return $this;
    }

    public function &genomes(): array
    {
        return $this->genomes;
    }

    public function getVectors(): array
    {
        $vects = [];
        foreach ($this->getGenomes() as $genomeId => $genome) {
            $vects[$genomeId] = $genome->getVector();
        }
        return $vects;
    }

    public function getSpecies(): array
    {
        return $this->species;
    }

    public function assignGenomesToSpecies(array $genomesId, ?int $speciesId): GenomePoolInterface
    {
        foreach ($genomesId as $genomeId) {
            $currentSpeciesId = $this->genomes[$genomeId]->getSpecies();

            // Genome previously had a species
            if (null !== $currentSpeciesId) {
                unset($this->species[$currentSpeciesId][array_search($genomeId, $this->species[$currentSpeciesId])]);
                // Remove species if empty
                if (empty($this->species[$currentSpeciesId])) {
                    unset($this->species[$currentSpeciesId]);
                }
            }

            // Set genome species
            $this->genomes[$genomeId]->setSpecies($speciesId);
            if (null !== $speciesId) {
                $this->species[$speciesId][] = $genomeId;
            }
        }

        // Sort species
        if (null !== $speciesId) {
            sort($this->species[$speciesId]);
            ksort($this->species);
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
