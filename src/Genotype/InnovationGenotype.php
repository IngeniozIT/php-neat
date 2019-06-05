<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\InnovationGenotypeInterface;

abstract class InnovationGenotype implements InnovationGenotypeInterface
{
    /**
     * Innovation number.
     *
     * @var int
     * @internal
     */
    protected $innovNb;

    /**
     * Constructor.
     *
     * @param int $innovNb Innovation number
     */
    public function __construct(int $innovNb)
    {
        $this->innovNb = $innovNb;
    }

    /**
     * Get the innovation number.
     *
     * @return int
     */
    public function innovNb(): int
    {
        return $this->innovNb;
    }
}
