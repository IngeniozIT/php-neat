<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents;

use IngeniozIT\Neat\Agents\Interfaces\AgentInterface;

class Agent extends Genome implements AgentInterface
{
    protected $fitness = null;

    /**
     * Get the fitness of the agent.
     *
     * @return float|null
     */
    public function fitness(): ?float
    {
        return $this->fitness;
    }

    /**
     * Set the fitness of the agent.
     *
     * @param ?float $fitness
     */
    public function setFitness(?float $fitness): void
    {
        $this->fitness = $fitness;
    }
}
