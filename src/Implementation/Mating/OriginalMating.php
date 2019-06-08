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

    protected $elitesPct;
    protected $untouchedElites;
    protected $weightMutationRate;
    protected $uniformWeightMutationRate;
    protected $newNodeMutationRate;
    protected $newLinkMutationRate;
    protected $interSpeciesBreedingRate;

    /**
     * Constructor.
     *
     * @param float $elitesPct Percentage of each species population that will mate and live to the next generation.
     * @param integer $untouchedElites Number of elites of each species that will not mutate.
     *
     * @see http://nn.cs.utexas.edu/downloads/papers/stanley.gecco02_1.pdf - section 2.3 and 2.4
     */
    public function __construct(
        float $elitesPct = 0.4,
        int $untouchedElites = 1,
        float $weightMutationRate = 0.8,
        float $uniformWeightMutationRate = 0.9,
        float $newNodeMutationRate = 0.03,
        float $newLinkMutationRate = 0.05,
        float $interSpeciesBreedingRate = 0.001
    )
    {
        $this->elitesPct = $elitesPct;
        $this->untouchedElites = $untouchedElites;
        $this->weightMutationRate = $weightMutationRate;
        $this->uniformWeightMutationRate = $uniformWeightMutationRate;
        $this->newNodeMutationRate = $newNodeMutationRate;
        $this->newLinkMutationRate = $newLinkMutationRate;
        $this->interSpeciesBreedingRate = $interSpeciesBreedingRate;
    }

    public function __invoke(PoolInterface $pool): void
    {
        $nbOffsprings = $this->getNbOffsprings($pool);
        $this->removeAgents($pool);
        $this->mateAgents($pool, $nbOffsprings);
    }

    protected function mateAgents(PoolInterface $pool, array $popTarget): void
    {
        $agentFactory = $pool->agentFactory();
        $offsprings = [];
        $species = $pool->getSpecies();
        $allowInterspecies = count($species) > 1;
        foreach ($species as $speciesId => $agentIds) {
            $delta = $popTarget[$speciesId] - count($agentIds);
            if ($delta < 0) {
                while ($delta < 0) {
                    $pool->removeAgent(array_pop($agentIds));
                    ++$delta;
                }
            } elseif ($delta > 0) {
                while ($delta > 0) {
                    // Chose parents
                    list($p1, $p2) = $this->choseParents($pool, $agentIds, $allowInterspecies);
                    // Reproduce parents
                    list($offspringNodeGenes, $offspringConnectGenes) = ($p1->fitness() > $p2->fitness()) ?
                        $this->getOffspringGenes($p1, $p2) :
                        $this->getOffspringGenes($p2, $p1);
                    // Mutate offspring
                    $this->mutateOffspring($pool, $offspringNodeGenes, $offspringConnectGenes);
                    // Add offspring to pool
                    $offsprings[] = $agentFactory->createAgent($offspringNodeGenes, $offspringConnectGenes);
                    --$delta;
                }
            }
        }
        foreach ($offsprings as $offspring) {
            $pool->addAgent($offspring);
        }
    }

    protected function mutateOffspring(PoolInterface $pool, array &$nodeGenes, array &$connectGenes): void
    {
        $genotypeFactory = $pool->genotypeFactory();
        // Mutate weights
        if (Random::frand() <= $this->weightMutationRate) {
            foreach ($connectGenes as $connectGene) {
                if (Random::frand() <= $this->uniformWeightMutationRate) {
                    $newWeight = $connectGene->weight() + Random::nrand(0, 1);
                } else {
                    $newWeight = Random::frand(-10, 10);
                }
                $connectGene->setWeight(min(10, max(-10, $newWeight)));
            }
        }
        // Add node mutation
        if (Random::frand() <= $this->newNodeMutationRate) {
            $mutatingConnection = $this->choseArrayValue($connectGenes);
            list($newNode, $newConnections) = $genotypeFactory->splitConnectGene(
                $mutatingConnection,
                $this->choseArrayValue($pool->activationFunctions()),
                $this->choseArrayValue($pool->aggregationFunctions())
            );
            if (!isset($nodeGenes[$newNode->innovNb()])) {
                // Disable connection
                $mutatingConnection->setDisabled(true);
                // Add node
                $nodeGenes[$newNode->innovNb()] = $newNode;
                // Add in connection
                $connectGenes[$newConnections[0]->innovNb()] = $newConnections[0];
                // Add out connection
                $connectGenes[$newConnections[1]->innovNb()] = $newConnections[1];
            }
        }
        // New link mutation
        if (Random::frand() <= $this->newLinkMutationRate) {
            do {
                $inNode = $this->choseArrayValue($nodeGenes);
            } while ($inNode->isOutput());
            $inInnovNb = $inNode->innovNb();
            do {
                $outNode = $this->choseArrayValue($nodeGenes);
            } while ($outNode->isSensor() || $inInnovNb === $outNode->innovNb());
            $newConnection = $genotypeFactory->createConnectGene(
                $inInnovNb,
                $outNode->innovNb(),
                Random::frand(-10, 10)
            );
            if (null !== $newConnection) {
                $innovNb = $newConnection->innovNb();
                if (!isset($connectGenes[$innovNb])) {
                    $connectGenes[$innovNb] = $newConnection;
                } elseif ($connectGenes[$innovNb]->isDisabled()) {
                    $connectGenes[$innovNb]->setDisabled(false);
                }
            }
        }
    }

    protected function choseParents(PoolInterface $pool, array $agentIds, bool $allowInterspecies = true): array
    {
        $p1 = $pool->agentNb($this->choseArrayValue($agentIds));

        if ($allowInterspecies && Random::frand() <= $this->interSpeciesBreedingRate) {
            // Chose parent 2 from other species
            do {
                $p2 = $this->choseArrayValue($pool->agents());
            } while ($p2->species() === $p1->species());
        } else {
            $p2 = $pool->agentNb($this->choseArrayValue($agentIds));
        }

        return [$p1, $p2];
    }

    /**
     * Compute the target number of offsprings for each species.
     *
     * @param  PoolInterface $pool
     *
     * @return array NUmber of offsprings of each species in the form [speciesId => nbOffsprings].
     *
     * @see http://nn.cs.utexas.edu/downloads/papers/stanley.gecco02_1.pdf - section 2.3
     */
    protected function getNbOffsprings(PoolInterface $pool): array
    {
        $fitnesses = [];
        $speciesFitnesses = [];
        foreach ($pool->getSpecies() as $speciesId => $agentIds) {
            $nbAgents = count($agentIds);
            foreach ($agentIds as $agentId) {
                $agent = $pool->agentNb($agentId);
                $fitness = $agent->fitness() / $nbAgents;
                $fitnesses[] = $fitness;
                $speciesFitnesses[$agent->species()][] = $fitness;
            }
        }

        foreach ($speciesFitnesses as $speciesId => $fits) {
            $speciesFitnesses[$speciesId] = array_sum($fits);
        }

        $fitnesses = array_sum($fitnesses) / count($fitnesses);

        $nbOffsprings = [];
        foreach ($speciesFitnesses as $speciesId => $fitness) {
            $nbOffsprings[$speciesId] = round($fitness / $fitnesses);
        }

        $targetPop = $pool->populationSize();
        while (array_sum($nbOffsprings) < $targetPop) {
            ++$nbOffsprings[$this->choseArrayIndex($nbOffsprings)];
        }
        while (array_sum($nbOffsprings) > $targetPop) {
            do {
                $i = $this->choseArrayIndex($nbOffsprings);
            } while ($nbOffsprings[$i] <= 0);
            --$nbOffsprings[$i];
        }

        return $nbOffsprings;
    }

    /**
     * Remove the worst agents from each species.
     *
     * @param PoolInterface $pool
     *
     * @see http://nn.cs.utexas.edu/downloads/papers/stanley.gecco02_1.pdf - section 2.3
     */
    protected function removeAgents(PoolInterface $pool): void
    {
        $species = $pool->getSpecies();
        foreach ($species as $agents) {
            $speciesSize = count($agents);
            $keepAlive = floor($speciesSize * $this->elitesPct);

            for ($i = $keepAlive + 1; $i < $speciesSize; ++$i) {
                $pool->removeAgent($agents[$i]);
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
        $offNodeGenes = [];
        $offConnectGenes = [];

        // Connection genes
        $p1ConnectGenes = $parent1->connectGenes();
        $p2ConnectGenes = $parent2->connectGenes();

        $maxConnectInnovNb = max($parent1->maxConnectInnovation(), $parent2->maxConnectInnovation());
        for ($i = 1; $i <= $maxConnectInnovNb; ++$i) {
            if (!isset($p1ConnectGenes[$i])) {
                continue;
            }
            $nodeGene = !isset($p2ConnectGenes[$i]) || rand() % 2 ?
                $p1ConnectGenes[$i] :
                $p2ConnectGenes[$i];
            $offConnectGenes[$i] = clone $nodeGene;
            $offNodeGenes[$offConnectGenes[$i]->inId()] = true;
            $offNodeGenes[$offConnectGenes[$i]->outId()] = true;
        }

        // Node genes
        $p1NodeGenes = $parent1->nodeGenes();
        $p2NodeGenes = $parent2->nodeGenes();

        foreach ($p1NodeGenes as $nodeGene) {
            if ($nodeGene->isSensor() || $nodeGene->isOutput()) {
                $offNodeGenes[$nodeGene->innovNb()] = true;
            }
        }
        foreach ($offNodeGenes as $i => &$foo) {
            if (!isset($p2NodeGenes[$i]) || rand() % 2) {
                $foo = clone $p1NodeGenes[$i];
            } else {
                $foo = clone $p2NodeGenes[$i];
            }
        }

        return [$offNodeGenes, $offConnectGenes];
    }
}
