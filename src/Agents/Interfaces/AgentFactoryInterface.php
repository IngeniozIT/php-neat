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

    /**
     * Create an AgentInterface from two AgentInterface parents.
     *
     * @param  AgentInterface $parent1 The most fit parent.
     * @param  AgentInterface $parent2 The less fit parent.
     *
     * @return AgentInterface
     */
    public function createAgentFromParents(AgentInterface $parent1, AgentInterface $parent2): AgentInterface;

    /**
     * Create an GenomeInterface from two GenomeInterface parents.
     *
     * @param  GenomeInterface $parent1 The most fit parent.
     * @param  GenomeInterface $parent2 The less fit parent.
     *
     * @return GenomeInterface
     */
    public function createGenomeFromParents(GenomeInterface $parent1, GenomeInterface $parent2): GenomeInterface;
}
