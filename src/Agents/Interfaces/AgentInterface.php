<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents\Interfaces;

interface AgentInterface extends GenomeInterface
{
    public function fitness(): ?float;
    public function setFitness(?float $fitness): void;
}
