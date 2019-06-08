<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Speciation;

use IngeniozIT\Neat\Implementation\Interfaces\SpeciationInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Implementation\Utils\ChoseArrayTrait;
use IngeniozIT\Neat\Implementation\Utils\AgentsComparator;
use IngeniozIT\Neat\Agents\Interfaces\AgentInterface;

/**
 * The original NEAT speciation algorithm.
 *
 * This algorithm is as presented by Kenneth O. Stanley and Risto Miikkulainen with the addition that it handles the
 * nodes activation and aggregation functions.
 *
 * @see http://nn.cs.utexas.edu/downloads/papers/stanley.ec02.pdf - section 3.3
 */
class OriginalSpeciation implements SpeciationInterface
{
    use ChoseArrayTrait;

    protected $deltaThreshold;
    protected $c1;
    protected $c2;
    protected $c3;
    protected $c4;
    protected $c5;

    /**
     * Constructor.
     *
     * @param float $deltaThreshold Compatibility threshold to consider two agents belong to the same species.
     * @param float $c1 Importance of excess genes.
     * @param float $c2 Importance of disjoint genes.
     * @param float $c3 Importance of weight differences.
     * @param float $c4 Importance of activation function mismatches.
     * @param float $c5 Importance of aggregation function mismatches.
     *
     * @see http://nn.cs.utexas.edu/downloads/papers/stanley.ec02.pdf - section 3.3
     */
    public function __construct(float $deltaThreshold, float $c1, float $c2, float $c3, float $c4 = 0, float $c5 = 0)
    {
        $this->deltaThreshold = $deltaThreshold;
        $this->c1 = $c1;
        $this->c2 = $c2;
        $this->c3 = $c3;
        $this->c4 = $c4;
        $this->c5 = $c5;
    }

    /**
     * Classify a PoolInterface's agents into species.
     *
     * @param  PoolInterface $pool
     */
    public function __invoke(PoolInterface $pool): void
    {
        // Gets species representants
        $currentSpecies = $pool->getSpecies();
        $ignoreActFns = count($pool->activationFunctions()) === 1;
        $ignoreAggrFns = count($pool->aggregationFunctions()) === 1;

        $species = [];
        foreach ($currentSpecies as $speciesId => $agentIds) {
            if (!\is_int($speciesId)) {
                continue;
            }
            $species[$speciesId] = [$this->choseArrayIndex($agentIds)];
            unset($currentSpecies[$speciesId][$species[$speciesId][0]]);
        }

        // Loop through each genome
        foreach ($pool as $agentId => $agent) {
            $assigned = false;
            foreach ($species as $speciesId => $agentIds) {
                $agentsComparator = new AgentsComparator($pool->agentNb($agentIds[0]), $pool->agentNb($agentId));
                // Assign to first matching species
                if ($this->getDistance($agentsComparator, $ignoreActFns, $ignoreAggrFns) <= $this->deltaThreshold) {
                    $assigned = true;
                    $species[$speciesId][] = $agentId;
                }
            }
            // Create new species
            if (!$assigned) {
                $species[] = [$agentId];
            }
        }

        // Assign genomes to species
        foreach ($species as $speciesId => $agentIds) {
            $pool->assignSpecies($speciesId, $agentIds);
        }
    }

    /**
     * Compute the distance betweek two agents.
     *
     * @param  AgentInterface $a1
     * @param  AgentInterface $a2
     * @param  boolean $ignoreActFns True to ignore activation functions.
     * @param  boolean $ignoreAggregationFunctions True to ignore aggregation functions.
     *
     * @return float
     *
     * @see http://nn.cs.utexas.edu/downloads/papers/stanley.ec02.pdf - section 3.3
     */
    public function getDistance(AgentsComparator $agentsComparator, bool $ignoreActFns = false, bool $ignoreAggrFns = false): float
    {
        $maxConnectGenesNb = $agentsComparator->maxConnectNb();
        $maxNodeGenesNb = $agentsComparator->maxNodeNb();

        return (
            $this->c1 * $agentsComparator->excessGenesNb() +
            $this->c2 * $agentsComparator->disjointGenesNb()
        ) / $maxConnectGenesNb +
        $this->c3 * $agentsComparator->avgWeightDifference() +
        (
            ($ignoreActFns ? 0 : $this->c4 * $agentsComparator->activationFnDifference()) +
            ($ignoreAggrFns ? 0 : $this->c5 * $agentsComparator->aggregationFnDifference())
        ) / $maxConnectGenesNb;
    }
}
