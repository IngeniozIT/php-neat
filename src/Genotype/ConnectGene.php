<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\ConnectGeneInterface;

class ConnectGene extends ConnectGenotype implements ConnectGeneInterface
{
    protected $weight;
    protected $disabled;

    public function __construct(int $innovNb, int $inId, int $outId, float $weight, bool $disabled)
    {
        parent::__construct($innovNb, $inId, $outId);

        $this->weight = $weight;
        $this->disabled = $disabled;
    }

    public function weight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
