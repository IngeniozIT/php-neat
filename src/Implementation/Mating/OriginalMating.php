<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Mating;

use IngeniozIT\Neat\Implementation\Interfaces\MatingInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Agents\Interfaces\GenomeInterface;
use IngeniozIT\Neat\Agents\Interfaces\AgentInterface;
use IngeniozIT\Math\Random;
use IngeniozIT\Neat\Implementation\Utils\ChoseArrayTrait;

class OriginalMating implements MatingInterface
{
    use ChoseArrayTrait;

    public function __invoke(PoolInterface $pool): void
    {
        $agentFactory = $pool->agentFactory();

        $this->removeAgents($pool);
        $species = $pool->getSpecies();
        $offspringsNb = $this->getNbOffsprings($species, $pool->populationSize());

        foreach ($offspringsNb as $speciesId => $targetPopSize) {
            $popSizeDelta = count($species[$speciesId]) - $targetPopSize;
            while ($popSizeDelta < 0) {
                $parent1 = $pool->agentNb($this->choseArrayValue($species[$speciesId]));
                $parent2 = $pool->agentNb($this->choseArrayValue($species[$speciesId]));

                list($nodeGenes, $connectGenes) = $parent1->fitness() > $parent2->fitness() ?
                    $this->getOffspringGenes($parent1, $parent2) :
                    $this->getOffspringGenes($parent2, $parent1);
                $connectGenesNb = count($connectGenes);
                foreach ($connectGenes as $connectGene) {
                    if (Random::frand() < 1 / $connectGenesNb) {
                        $connectGene->setWeight(max(-100, min(100, $connectGene->weight() * Random::nrand(1, 1 / 3))));
                    }
                }
                $agent = $agentFactory->createAgent($nodeGenes, $connectGenes);
                $agent->setSpecies($speciesId);
                $pool->addAgent($agent);
                ++$popSizeDelta;
            }
        }

        // echo 'POOL', PHP_EOL;
        // foreach ($pool as $id => $agent) {
        //     echo $id, ' ', $agent->species(), ' ', $agent->fitness(), PHP_EOL;
        // }

        // $agentFactory = $pool->agentFactory();
        // $offsprings = [];
        //
        // $nbOffspringsToProduce = $pool->populationSize() - count($pool);
        // while ($nbOffspringsToProduce--) {
        //     $parent1 = $pool->agentNb($this->choseParent($pool));
        //     $parent2 = $pool->agentNb($this->choseParent($pool));
        //
        //     list($nodeGenes, $connectGenes) = $parent1->fitness() > $parent2->fitness() ?
        //         $this->getOffspringGenes($parent1, $parent2) :
        //         $this->getOffspringGenes($parent2, $parent1);
        //
        // }
        //
        // foreach ($offsprings as $offspring) {
        //     $pool->addAgent($offspring);
        // }
    }

    protected function choseParent(array $species): int
    {
        $nbAgents = count($species);
        $selectedAgent = rand(1, $nbAgents);
        foreach ($species as $agentNb => $fitnes) {
            if (!--$selectedAgent) {
                return $agentNb;
            }
        }
    }

    protected function removeAgents(PoolInterface $pool): void
    {
        $popSize = $pool->populationSize();

        $i = 0;
        foreach ($pool as $agentId => $agent) {
            if (Random::frand() <= ($i++ / $popSize) ** 0.5) {
                echo 'Removing agent ', $agentId, PHP_EOL;
                $pool->removeAgent($agentId);
            }
        }
    }

    protected function getNbOffsprings(array $species, int $targetPopSize): array
    {
        $popSizes = [];
        foreach ($species as $i => $s) {
            $popSizes[$i] = count($s);
            $species[$i] = array_sum($s) / $popSizes[$i];
        }
        $avgFitness = array_sum($species) / count($species);

        foreach ($species as $i => $s) {
            $species[$i] = round($s / $avgFitness * $popSizes[$i]);
        }

        while (array_sum($species) > $targetPopSize) {
            --$species[$this->choseSpecies($species)];
        }

        while (array_sum($species) < $targetPopSize) {
            ++$species[$this->choseSpecies($species)];
        }

        return $species;
    }

    protected function choseSpecies(array $species): int
    {
        $nbAgents = (int)array_sum($species) - count($species);

        $chosenSpecies = rand(0, $nbAgents);
        foreach ($species as $speciesId => $popSize) {
            $chosenSpecies -= $popSize - 1;
            if ($chosenSpecies <= 0) {
                return $speciesId;
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
