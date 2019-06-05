<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface NodeGenotypeInterface extends InnovationGenotypeInterface
{
    /**
     * Get the type of the node.
     *
     * @return int Either NodeGenotype::NODE_SENSOR, NodeGenotype::NODE_OUTPUT or NodeGenotype::NODE_HIDDEN.
     */
    public function type(): int;

    /**
     * Check if the node is a sensor (input) node.
     *
     * @return bool
     */
    public function isSensor(): bool;

    /**
     * Check if the node is an output node.
     *
     * @return bool
     */
    public function isOutput(): bool;

    /**
     * Check if the node is a hidden node.
     *
     * @return bool
     */
    public function isHidden(): bool;
}
