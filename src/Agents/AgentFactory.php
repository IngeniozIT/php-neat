<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents;

use IngeniozIT\Neat\Agents\Interfaces\AgentFactoryInterface;
use IngeniozIT\Neat\Agents\Interfaces\AgentInterface;
use IngeniozIT\Neat\Agents\Interfaces\GenomeInterface;

class AgentFactory implements AgentFactoryInterface
{
    /**
     * Create an AgentInterface from genes.
     *
     * @param  NodeGeneInterface[] $nodeGenes The NodeGeneInterface items to add to the agent.
     * @param  ConnectGeneInterface[] $connectGenes The ConnectGeneInterface items to add to the agent.
     *
     * @return AgentInterface
     */
    public function createAgent(array $nodeGenes = [], array $connectGenes = []): AgentInterface
    {
        return $this->populateGenome($this->getNewAgent(), $nodeGenes, $connectGenes);
    }

    /**
     * Create an GenomeInterface from genes.
     *
     * @param  NodeGeneInterface[] $nodeGenes The NodeGeneInterface items to add to the genome.
     * @param  ConnectGeneInterface[] $connectGenes The ConnectGeneInterface items to add to the genome.
     *
     * @return GenomeInterface
     */
    public function createGenome(array $nodeGenes = [], array $connectGenes = []): GenomeInterface
    {
        return $this->populateGenome($this->getNewGenome(), $nodeGenes, $connectGenes);
    }

    /**
     * Return a new instance of an AgentInterface.
     *
     * @return AgentInterface
     */
    protected function getNewAgent(): AgentInterface
    {
        return new Agent();
    }

    /**
     * Return a new instance of a GenomeInterface.
     *
     * @return GenomeInterface
     */
    protected function getNewGenome(): GenomeInterface
    {
        return new Genome();
    }

    /**
     * Add genes to a GenomeInterface.
     *
     * @param  GenomeInterface $genome
     * @param  NodeGeneInterface[] $nodeGenes
     * @param  ConnectGeneInterface[] $connectGenes
     *
     * @return GenomeInterface
     */
    protected function populateGenome(GenomeInterface $genome, array $nodeGenes, array $connectGenes): GenomeInterface
    {
        foreach ($nodeGenes as $nodeGene) {
            $genome->addNodeGene($nodeGene);
        }
        foreach ($connectGenes as $connectGene) {
            $genome->addConnectGene($connectGene);
        }

        return $genome;
    }
}
