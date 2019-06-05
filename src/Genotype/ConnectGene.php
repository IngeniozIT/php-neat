<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\ConnectGeneInterface;

class ConnectGene extends ConnectGenotype implements ConnectGeneInterface
{
    /**
     * Connection weight.
     *
     * @var float
     * @internal
     */
    protected $weight;

    /**
     * Connection disabled/enabled status.
     *
     * @var bool
     * @internal
     */
    protected $disabled;

    /**
     * Constructor.
     *
     * @param int $innovNb Innovation number.
     * @param int $inId Innovation number of the input node.
     * @param int $outId Innovation number of the output node.
     * @param float $weight Initial connection weight.
     * @param bool $disabled True if the connection is disabled, false otherwise.
     */
    public function __construct(int $innovNb, int $inId, int $outId, float $weight, bool $disabled)
    {
        parent::__construct($innovNb, $inId, $outId);

        $this->weight = $weight;
        $this->disabled = $disabled;
    }

    /**
     * Get the connection weight.
     *
     * @return float
     */
    public function weight(): float
    {
        return $this->weight;
    }

    /**
     * Set the connection weight.
     *
     * @param float $weight
     */
    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * Check if the connection gene is disabled.
     *
     * @return bool True if it is disabled, false otherwise.
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Set the disabled state of the connection gene.
     *
     * @param bool $disabled True to disable, false to enable.
     */
    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
