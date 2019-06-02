<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype\Interfaces;

interface ConnectGeneInterface extends ConnectGenotypeInterface
{
    public function weight(): float;
    public function setWeight(float $weight): void;
    public function isDisabled(): bool;
    public function setDisabled(bool $disabled): void;
}
