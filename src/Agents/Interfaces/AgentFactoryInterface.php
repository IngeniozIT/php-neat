<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents\Interfaces;

interface AgentFactoryInterface
{
    /**
     * Create an AgentInterface from genes.
     *
     * @param  NodeGeneInterface[] $nodeGenes The NodeGeneInterface items to add to the agent.
     * @param  ConnectGeneInterface[] $connectGenes The ConnectGeneInterface items to add to the agent.
     *
     * @return AgentInterface
     */
    public function createAgent(array $nodeGenes = [], array $connectGenes = []): AgentInterface;

    /**
     * Create an GenomeInterface from genes.
     *
     * @param  NodeGeneInterface[] $nodeGenes The NodeGeneInterface items to add to the genome.
     * @param  ConnectGeneInterface[] $connectGenes The ConnectGeneInterface items to add to the genome.
     *
     * @return GenomeInterface
     */
    public function createGenome(array $nodeGenes = [], array $connectGenes = []): GenomeInterface;
}
