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
        float $newLinkMutationRate = 1 / 150 ** 0.5,
        float $interSpeciesBreedingRate = 0.2
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
        $species = $pool->getSpecies();
        $genotypeFactory = $pool->genotypeFactory();
        $agentFactory = $pool->agentFactory();
        $activationFunctions = $pool->activationFunctions();
        $aggregationFunctions = $pool->aggregationFunctions();

        foreach ($species as $speciesId => $agents) {
            $speciesCount = count($agents);
            for ($i = $popTarget[$speciesId] - $speciesCount; $i > 0; --$i) {
                // Chose parents
                $parent1Id = $agents[rand(0, $speciesCount - 1)];
                if (Random::frand() <= $this->interSpeciesBreedingRate && count($species) > 1) {
                    // Chose parent 2 from other species
                    do {
                        $parent2Id = $this->choseArrayIndex($pool);
                    } while (\in_array($parent2Id, $agents));
                } else {
                    $parent2Id = $agents[rand(0, $speciesCount - 1)];
                }
                // Reproduce parents
                $parent1 = $pool->agentNb($parent1Id);
                $parent2 = $pool->agentNb($parent2Id);
                list($offspringNodeGenes, $offspringConnectGenes) = ($parent1->fitness() > $parent2->fitness()) ?
                    $this->getOffspringGenes($parent1, $parent2) :
                    $this->getOffspringGenes($parent2, $parent1);
                // Weight mutation
                if (Random::frand() <= $this->weightMutationRate) {
                    foreach ($offspringConnectGenes as $connectGene) {
                        if (Random::frand() <= $this->uniformWeightMutationRate) {
                            $newWeight = $connectGene->weight() + Random::nrand(0, 0.1);
                        } else {
                            $newWeight = Random::frand(-10, 10);
                        }
                        $connectGene->setWeight(max(-10, min(10, $newWeight)));
                    }
                }
                // Add node mutation
                if (Random::frand() <= $this->newNodeMutationRate) {
                    $mutatingConnection = $this->choseArrayValue($offspringConnectGenes);
                    list($newNode, $newConnections) = $genotypeFactory->splitConnectGene(
                        $mutatingConnection,
                        $this->choseArrayValue($activationFunctions),
                        $this->choseArrayValue($aggregationFunctions)
                    );
                    if (!isset($offspringNodeGenes[$newNode->innovNb()])) {
                        // Disable connection
                        $mutatingConnection->setDisabled(true);
                        // Add node
                        $offspringNodeGenes[$newNode->innovNb()] = $newNode;
                        // Add in connection
                        $offspringConnectGenes[$newConnections[0]->innovNb()] = $newConnections[0];
                        // Add out connection
                        $offspringConnectGenes[$newConnections[1]->innovNb()] = $newConnections[1];
                    }
                }
                // New link mutation
                foreach ($offspringNodeGenes as $inNode) {
                    if ($inNode->isOutput()) {
                        continue;
                    }
                    if (Random::frand() <= $this->newLinkMutationRate) {
                        $inInnovNb = $inNode->innovNb();
                        // do {
                        //     $inNode = $this->choseArrayValue($offspringNodeGenes);
                        // } while ($inNode->isOutput());
                        do {
                            $outNode = $this->choseArrayValue($offspringNodeGenes);
                        } while ($outNode->isSensor() || $inInnovNb === $outNode->innovNb());
                        $newConnection = $genotypeFactory->createConnectGene(
                            $inInnovNb,
                            $outNode->innovNb(),
                            Random::frand(-10, 10)
                        );
                        if (null === $newConnection) {
                            continue;
                        }
                        $innovNb = $newConnection->innovNb();
                        if (!isset($offspringConnectGenes[$innovNb])) {
                            $offspringConnectGenes[$innovNb] = $newConnection;
                        } elseif ($offspringConnectGenes[$innovNb]->isDisabled()) {
                            $offspringConnectGenes[$innovNb]->setDisabled(false);
                        }
                    }
                }
                // Add offspring to pool
                $offspring = $agentFactory->createAgent($offspringNodeGenes, $offspringConnectGenes);
                $offspring->setSpecies($parent1->species());
                $pool->addAgent($offspring);
            }
        }
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
        $nbOffsprings = [];

        $popSizes = [];
        $fitnesses = [];
        foreach ($pool as $agentId => $agent) {
            $species = $agent->species();
            $fitnesses[$species][$agentId] = $agent->fitness();
            if (!isset($popSizes[$species])) {
                $popSizes[$species] = 1;
            } else {
                ++$popSizes[$species];
            }
        }
        foreach ($fitnesses as $speciesId => $fits) {
            $fitnesses[$speciesId] = array_sum($fits) / count($fits);
        }
        $globalFitness = array_sum($fitnesses) / count($fitnesses);

        foreach ($fitnesses as $speciesId => $fitness) {
            $nbOffsprings[$speciesId] = round($fitness / $globalFitness * $popSizes[$speciesId]);
        }

        $nbAgents = count($pool);
        while (array_sum($nbOffsprings) < $nbAgents) {
            ++$nbOffsprings[$this->choseArrayIndex($nbOffsprings)];
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

        foreach ($parent1NodeGenes as $innovNb => $nodeGene) {
            $maxNodeInnovId = max($maxNodeInnovId, $innovNb);
            if (!$nodeGene->isHidden()) {
                $mandatoryNodeGenes[$innovNb] = true;
            }
        }
        foreach ($parent2NodeGenes as $connectGene) {
            $maxNodeInnovId = max($maxNodeInnovId, $nodeGene->innovNb());
            if (!$nodeGene->isHidden()) {
                $mandatoryNodeGenes[$innovNb] = true;
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
        for ($innovNb = 1; $innovNb <= $maxNodeInnovId; ++$innovNb) {
        // foreach ($mandatoryNodeGenes as $innovNb => $foo) {
            if (
                isset($parent1NodeGenes[$innovNb]) &&
                (
                    !isset($parent2NodeGenes[$innovNb]) ||
                    rand() % 2
                )
            ) {
                $selectedGene = $parent1NodeGenes[$innovNb];
            } elseif (isset($parent2NodeGenes[$innovNb])) {
                $selectedGene = $parent2NodeGenes[$innovNb];
            } else {
                continue;
            }
            $offspringNodeGenes[$innovNb] = clone $selectedGene;
        }

        return [$offspringNodeGenes, $offspringConnectGenes];
    }
}
