<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents;

use IngeniozIT\Neat\Agents\Interfaces\AgentFactoryInterface;
use IngeniozIT\Neat\Agents\Interfaces\GenomeInterface;
use IngeniozIT\Neat\Genotype\Interfaces\SensorNodeGeneInterface;
use IngeniozIT\Neat\Genotype\Interfaces\OutputNodeGeneInterface;

class AgentFactory implements AgentFactoryInterface
{
    public function createGenome(array $nodeGenes = [], array $connectGenes = []): GenomeInterface
    {
        $genome = new Genome();

        foreach ($nodeGenes as $nodeGene) {
            $genome->addNodeGene($nodeGene);
        }
        foreach ($connectGenes as $connectGene) {
            $genome->addConnectGene($connectGene);
        }

        return $genome;
    }

    public function createGenomeFromParents(GenomeInterface $parent1, GenomeInterface $parent2): GenomeInterface
    {
        // Parent connexion genees
        $maxConnectInnovId = 0;
        $parent1ConnectGenes = $parent1->connectGenes();
        $parent2ConnectGenes = $parent2->connectGenes();
        foreach ($parent1ConnectGenes as $innovId => $connectGene) {
            $maxConnectInnovId = max($maxConnectInnovId, $innovId);
        }
        foreach ($parent2ConnectGenes as $innovId => $connectGene) {
            $maxConnectInnovId = max($maxConnectInnovId, $innovId);
        }

        // Parent node genes
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

        // Offspring connect genes
        $offspringConnectGenes = [];
        for ($i = 1; $i <= $maxConnectInnovId; ++$i) {
            if (!isset($parent1ConnectGenes[$i])) {
                // Do not inherit gene
                continue;
            } elseif (isset($parent2ConnectGenes[$i])) {
                // Inherit gene from either parent
                $offspringConnectGenes[$i] = clone rand() % 2 ?
                    $parent1ConnectGenes[$i] :
                    $parent2ConnectGenes[$i];
            } else {
                // Inherit gene from most fit parent
                $offspringConnectGenes[$i] = clone $parent1ConnectGenes[$i];
            }
            $mandatoryNodeGenes[$offspringConnectGenes[$i]->inId()] = true;
            $mandatoryNodeGenes[$offspringConnectGenes[$i]->outId()] = true;
        }

        // Offspring node genes
        $offspringNodeGenes = [];
        for ($i = 1; $i <= $maxNodeInnovId; ++$i) {
            if (!isset($mandatoryNodeGenes[$i])) {
                // Do not inherit gene
                continue;
            } elseif (isset($parent2NodeGenes[$i])) {
                // Inherit gene from either parent
                $offspringNodeGenes[$i] = clone rand() % 2 ?
                    $parent1NodeGenes[$i] :
                    $parent2NodeGenes[$i];
            } else {
                // Inherit gene from most fit parent
                $offspringNodeGenes[$i] = clone $parent1NodeGenes[$i];
            }
        }

        return $this->createGenome($offspringNodeGenes, $offspringConnectGenes);
    }
}
