<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface;
use IngeniozIT\Neat\Exceptions\InvalidArgumentException;

class NodeGenotype extends InnovationGenotype implements NodeGenotypeInterface
{
    const TYPE_SENSOR = 0b001;
    const TYPE_OUTPUT = 0b010;
    const TYPE_HIDDEN = 0b100;

    /**
     * @var int
     */
    protected $type;

    /**
     * Constructor.
     *
     * @param int $innovNb Innovation number
     * @param int $type The node type. Either NodeGenotype::NODE_SENSOR, NodeGenotype::NODE_OUTPUT or
     * NodeGenotype::NODE_HIDDEN.
     */
    public function __construct(int $innovNb, int $type)
    {
        if ($type !== self::TYPE_SENSOR
            && $type !== self::TYPE_OUTPUT
            && $type !== self::TYPE_HIDDEN
        ) {
            throw new InvalidArgumentException("Type $type is not a valid node type.");
        }

        parent::__construct($innovNb);
        $this->type = $type;
    }

    /**
     * Get the type of the node.
     *
     * @return int Either NodeGenotype::NODE_SENSOR, NodeGenotype::NODE_OUTPUT or NodeGenotype::NODE_HIDDEN.
     */
    public function type(): int
    {
        return $this->type;
    }

    /**
     * Check if the node is a sensor (input) node.
     *
     * @return bool
     */
    public function isSensor(): bool
    {
        return self::TYPE_SENSOR === $this->type;
    }

    /**
     * Check if the node is an output node.
     *
     * @return bool
     */
    public function isOutput(): bool
    {
        return self::TYPE_OUTPUT === $this->type;
    }

    /**
     * Check if the node is a hidden node.
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return self::TYPE_HIDDEN === $this->type;
    }
}
