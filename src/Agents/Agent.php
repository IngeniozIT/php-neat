<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents;

use IngeniozIT\Neat\Agents\Interfaces\AgentInterface;

class Agent extends Genome implements AgentInterface
{
    protected $fitness = null;
    protected $species = null;

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

    /**
     * Get the species of the agent.
     *
     * @return int|null
     */
    public function species(): ?int
    {
        return $this->species;
    }

    /**
     * Set the species of the agent.
     *
     * @param ?int $species
     */
    public function setSpecies(?int $species): void
    {
        $this->species = $species;
    }

    public function __toString(): string
    {
        $arr = [
            'species' => $this->species()
        ];
        foreach ($this->nodeGenes() as $nodeGene) {
            $arr['nodes'][] = $nodeGene->innovNb();
        }
        foreach ($this->connectGenes as $conn) {
            $arr['connect'][] = $conn->innovNb().' '.$conn->inId().' '.$conn->outId().' '.$conn->weight().' '.$conn->isDisabled();
        }
        return json_encode($arr, JSON_PRETTY_PRINT);
    }
}
