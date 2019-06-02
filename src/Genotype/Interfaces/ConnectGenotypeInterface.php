<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface ConnectGenotypeInterface extends InnovationGenotypeInterface
{
    /**
     * Get the innovation number of the input node.
     *
     * @return int
     */
    public function inId(): int;

    /**
     * Get the innovation number of the output node.
     *
     * @return int
     */
    public function outId(): int;
}
