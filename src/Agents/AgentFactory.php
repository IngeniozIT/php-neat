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
     * Create an AgentInterface from two AgentInterface parents.
     *
     * @param  AgentInterface $parent1 The most fit parent.
     * @param  AgentInterface $parent2 The less fit parent.
     *
     * @return AgentInterface
     */
    public function createAgentFromParents(AgentInterface $parent1, AgentInterface $parent2): AgentInterface
    {
        list($nodeGenes, $connectGenes) = $this->getOffspringGenes($parent1, $parent2);

        return $this->createAgent($nodeGenes, $connectGenes);
    }

    /**
     * Create an GenomeInterface from two GenomeInterface parents.
     *
     * @param  GenomeInterface $parent1 The most fit parent.
     * @param  GenomeInterface $parent2 The less fit parent.
     *
     * @return GenomeInterface
     */
    public function createGenomeFromParents(GenomeInterface $parent1, GenomeInterface $parent2): GenomeInterface
    {
        list($nodeGenes, $connectGenes) = $this->getOffspringGenes($parent1, $parent2);

        return $this->createGenome($nodeGenes, $connectGenes);
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

    /**
     * Combine the genes from two parents.
     * This method does not mutate the genes, it just selects which genes an offspring will inherit as explained in part
     * 3.2 ("Tracking Genes through Historical Markings") and figure 4 of the original NEAT article.
     *
     * @link http://nn.cs.utexas.edu/downloads/papers/stanley.ec02.pdf
     *
     * @param  GenomeInterface $parent1 The most fit parent.
     * @param  GenomeInterface $parent2 The less fit parent.
     *
     * @return array [NodeGeneInterface[], ConnectGeneInterface[]]
     */
    protected function getOffspringGenes(GenomeInterface $parent1, GenomeInterface $parent2): array
    {
        // Get parents connect genes
        $parent1ConnectGenes = $parent1->connectGenes();
        $parent2ConnectGenes = $parent2->connectGenes();
        $maxConnectInnovId = max(
            max(array_keys($parent1ConnectGenes)),
            max(array_keys($parent2ConnectGenes))
        );

        // Get parents node genes
        $maxNodeInnovId = 0;
        $mandatoryNodeGenes = [];
        $parent1NodeGenes = $parent1->nodeGenes();
        $parent2NodeGenes = $parent2->nodeGenes();
        foreach ($parent1NodeGenes as $innovId => $nodeGene) {
            $maxNodeInnovId = max($maxNodeInnovId, $innovId);
            if (!$nodeGene->isHidden()) {
                $mandatoryNodeGenes[$innovId] = true;
            }
        }
        foreach ($parent2NodeGenes as $connectGene) {
            $maxNodeInnovId = max($maxNodeInnovId, $nodeGene->innovId());
            if (!$nodeGene->isHidden()) {
                $mandatoryNodeGenes[$innovId] = true;
            }
        }

        // Select offspring connect genes and their corresponding node genes innovation ids
        $offspringConnectGenes = [];
        for ($i = 1; $i <= $maxConnectInnovId; ++$i) {
            if (!isset($parent1ConnectGenes[$i])) {
                // Do not inherit gene
                continue;
            }
            $offspringConnectGenes[$i] = clone (isset($parent2ConnectGenes[$i]) && rand() % 2) ?
                $parent2ConnectGenes[$i] :
                $parent1ConnectGenes[$i];
            $mandatoryNodeGenes[$offspringConnectGenes[$i]->inId()] = true;
            $mandatoryNodeGenes[$offspringConnectGenes[$i]->outId()] = true;
        }

        // Select node genes so each connect gene can be attached to an in and out node
        $offspringNodeGenes = [];
        foreach ($mandatoryNodeGenes as $innovId => $foo) {
            $offspringConnectGenes[$innovId] = clone (isset($parent2NodeGenes[$innovId]) && rand() % 2) ?
                $parent2NodeGenes[$innovId] :
                $parent1NodeGenes[$innovId];
        }

        return [$offspringNodeGenes, $offspringConnectGenes];
    }
}
