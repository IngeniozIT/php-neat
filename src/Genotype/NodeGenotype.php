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

    protected $type;

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

    public function type(): int
    {
        return $this->type;
    }

    public function isSensor(): bool
    {
        return self::TYPE_SENSOR === $this->type;
    }

    public function isOutput(): bool
    {
        return self::TYPE_OUTPUT === $this->type;
    }

    public function isHidden(): bool
    {
        return self::TYPE_HIDDEN === $this->type;
    }
}
