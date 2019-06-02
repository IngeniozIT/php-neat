<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents;

use IngeniozIT\Neat\Agents\Interfaces\AgentInterface;

class Agent extends Genome implements AgentInterface
{
    protected $fitness = null;

    public function fitness(): ?float
    {
        return $this->fitness;
    }

    public function setFitness(?float $fitness): void
    {
        $this->fitness = $fitness;
    }
}
