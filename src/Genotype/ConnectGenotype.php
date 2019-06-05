<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\ConnectGenotypeInterface;

class ConnectGenotype extends InnovationGenotype implements ConnectGenotypeInterface
{
    /**
     * Innovation number of the input node.
     *
     * @var int
     * @internal
     */
    protected $inId;

    /**
     * Innovation number of the output node.
     *
     * @var int
     * @internal
     */
    protected $outId;

    /**
     * Constructor.
     *
     * @param int $innovNb Innovation number.
     * @param int $inId Innovation number of the input node.
     * @param int $outId Innovation number of the output node.
     */
    public function __construct(int $innovNb, int $inId, int $outId)
    {
        parent::__construct($innovNb);

        $this->inId = $inId;
        $this->outId = $outId;
    }

    /**
     * Get the innovation number of the input node.
     *
     * @return int
     */
    public function inId(): int
    {
        return $this->inId;
    }

    /**
     * Get the innovation number of the output node.
     *
     * @return int
     */
    public function outId(): int
    {
        return $this->outId;
    }
}
