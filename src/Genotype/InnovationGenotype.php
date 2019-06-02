<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\InnovationGenotypeInterface;

abstract class InnovationGenotype implements InnovationGenotypeInterface
{
    protected $innovId;

    public function __construct(int $innovId)
    {
        $this->innovId = $innovId;
    }

    public function innovId(): int
    {
        return $this->innovId;
    }
}
