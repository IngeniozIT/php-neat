<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\ConnectGenotypeInterface;

class ConnectGenotype extends InnovationGenotype implements ConnectGenotypeInterface
{
    protected $inId;
    protected $outId;

    public function __construct(int $innovId, int $inId, int $outId)
    {
        parent::__construct($innovId);

        $this->inId = $inId;
        $this->outId = $outId;
    }

    public function inId(): int
    {
        return $this->inId;
    }

    public function outId(): int
    {
        return $this->outId;
    }
}
