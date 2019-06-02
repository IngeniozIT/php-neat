<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface ConnectGenotypeInterface extends InnovationGenotypeInterface
{
    public function inId(): int;
    public function outId(): int;
}
