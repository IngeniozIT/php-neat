<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\InnovationGenotypeInterface;

abstract class InnovationGenotype implements InnovationGenotypeInterface
{
    protected $innovNb;

    public function __construct(int $innovNb)
    {
        $this->innovNb = $innovNb;
    }

    public function innovNb(): int
    {
        return $this->innovNb;
    }
}
