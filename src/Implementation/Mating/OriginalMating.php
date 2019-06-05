<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Mating;

use IngeniozIT\Neat\Implementation\Interfaces\MatingInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Agents\Interfaces\GenomeInterface;
use IngeniozIT\Math\Random;

class OriginalMating implements MatingInterface
{
    public function __invoke(PoolInterface $pool): void
    {
        $agentFactory = $pool->agentFactory();
        $popSize = $pool->populationSize();

        $champion = $pool->agentNb(0);

        $passed = false;
        while (count($pool) < $popSize) {
            foreach ($pool as $agent) {
                if (count($pool) >= $popSize) {
                    return;
                }
                if (!$passed) {
                    $passed = true;
                    continue;
                }
                echo 'Adding new agent', PHP_EOL;
                list($nodeGenes, $connectGenes) = $this->getOffspringGenes($champion, $agent);

                $connectGenesNb = count($connectGenes);
                foreach ($connectGenes as $connectGene) {
                    if (Random::frand() < 1 / $connectGenesNb) {
                        $connectGene->setWeight($connectGene->weight() * Random::nrand(1, 0.5));
                    }
                }
                $pool->addAgent($agentFactory->createAgent($nodeGenes, $connectGenes));
            }
        }
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
            $maxNodeInnovId = max($maxNodeInnovId, $nodeGene->innovNb());
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
            $selectedGene = (isset($parent2ConnectGenes[$i]) && rand() % 2) ?
                $parent2ConnectGenes[$i] :
                $parent1ConnectGenes[$i];
            $offspringConnectGenes[$i] = clone $selectedGene;
            $mandatoryNodeGenes[$offspringConnectGenes[$i]->inId()] = true;
            $mandatoryNodeGenes[$offspringConnectGenes[$i]->outId()] = true;
        }

        // Select node genes so each connect gene can be attached to an in and out node
        $offspringNodeGenes = [];
        foreach ($mandatoryNodeGenes as $innovId => $foo) {
            $selectedGene = (isset($parent2NodeGenes[$innovId]) && rand() % 2) ?
                $parent2NodeGenes[$innovId] :
                $parent1NodeGenes[$innovId];
            $offspringNodeGenes[$innovId] = clone $selectedGene;
        }

        return [$offspringNodeGenes, $offspringConnectGenes];
    }
}
