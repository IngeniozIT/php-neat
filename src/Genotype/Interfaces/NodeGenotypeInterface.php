<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface NodeGenotypeInterface extends InnovationGenotypeInterface
{
    public function type(): int;
    public function isSensor(): bool;
    public function isOutput(): bool;
    public function isHidden(): bool;
}
