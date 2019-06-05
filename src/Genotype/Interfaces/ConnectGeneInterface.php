<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface ConnectGeneInterface extends ConnectGenotypeInterface
{
    /**
     * Get the connection weight.
     *
     * @return float
     */
    public function weight(): float;

    /**
     * Set the connection weight.
     *
     * @param float $weight
     */
    public function setWeight(float $weight): void;

    /**
     * Check if the connection gene is disabled.
     *
     * @return bool True if it is disabled, false otherwise
     */
    public function isDisabled(): bool;

    /**
     * Set the disabled state of the connection gene.
     *
     * @param bool $disabled True to disable, false to enable
     */
    public function setDisabled(bool $disabled): void;
}
