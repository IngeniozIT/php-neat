<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents\Interfaces;

interface AgentInterface extends GenomeInterface
{
    /**
     * Get the fitness of the agent.
     *
     * @return float|null
     */
    public function fitness(): ?float;

    /**
     * Set the fitness of the agent.
     *
     * @param ?float $fitness
     */
    public function setFitness(?float $fitness): void;
}
