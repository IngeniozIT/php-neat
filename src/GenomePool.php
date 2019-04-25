<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

use IngeniozIT\NEAT\Interfaces\GenomePoolInterface;
use IngeniozIT\NEAT\Interfaces\GenomeInterface;

class GenomePool implements GenomePoolInterface
{
    protected $genomes = [];

    public function addGenome(GenomeInterface &$genome): GenomePoolInterface
    {
        $this->genomes[] = $genome;

        return $this;
    }

    public function getGenomes(): array
    {
        return $this->genomes;
    }
}
