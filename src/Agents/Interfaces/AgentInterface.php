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

    /**
     * Get the species of the agent.
     *
     * @return int|null
     */
    public function species(): ?int;

    /**
     * Set the species of the agent.
     *
     * @param ?int $species
     */
    public function setSpecies(?int $species): void;
}
