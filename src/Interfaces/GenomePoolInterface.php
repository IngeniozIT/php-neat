<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Interfaces;

interface GenomePoolInterface
{
    public function addGenome(GenomeInterface &$genome): GenomePoolInterface;

    public function getGenomes(): array;
}
