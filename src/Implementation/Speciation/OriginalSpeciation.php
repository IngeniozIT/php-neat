<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Speciation;

use IngeniozIT\Neat\Implementation\Interfaces\SpeciationInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Implementation\Utils\ChoseArrayTrait;
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
        $ignoreActivationFunctions = count($pool->activationFunctions()) === 1;
        $ignoreAggregationFunctions = count($pool->aggregationFunctions()) === 1;

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
                // Assign to first matching species
                if ($this->getDistance($pool->agentNb($agentIds[0]), $pool->agentNb($agentId)) <= $this->deltaThreshold) {
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
     * @param  boolean $ignoreActivationFunctions True to ignore activation functions.
     * @param  boolean $ignoreAggregationFunctions True to ignore aggregation functions.
     *
     * @return float
     *
     * @see http://nn.cs.utexas.edu/downloads/papers/stanley.ec02.pdf - section 3.3
     */
    public function getDistance(AgentInterface $a1, AgentInterface $a2, bool $ignoreActivationFunctions = false, bool $ignoreAggregationFunctions = false): float
    {
        $maxConnectGenesNb = max($a1->connectGenesNb(), $a2->connectGenesNb());
        $maxNodeGenesNb = max($a1->nodeGenesNb(), $a2->nodeGenesNb());

        return (
            $this->c1 * $this->excessGenesNb($a1, $a2) +
            $this->c2 * $this->disjointGenesNb($a1, $a2)
        ) / $maxConnectGenesNb +
        $this->c3 * $this->avgWeightDifference($a1, $a2) +
        (
            ($ignoreActivationFunctions ? 0 : $this->c4 * $this->activationFnDifference($a1, $a2)) +
            ($ignoreAggregationFunctions ? 0 : $this->c5 * $this->aggregationFnDifference($a1, $a2))
        ) / $maxConnectGenesNb;
    }

    /**
     * Count how many excess genes exist between two agents.
     *
     * @param  AgentInterface $a1
     * @param  AgentInterface $a2
     *
     * @return int
     */
    public function excessGenesNb(AgentInterface $a1, AgentInterface $a2): int
    {
        $a1InnovNb = $a1->maxConnectInnovation();
        $a2InnovNb = $a2->maxConnectInnovation();

        $maxInnov = max($a1InnovNb, $a2InnovNb);
        $minInnov = min($a1InnovNb, $a2InnovNb);

        $a1Genes = $a1->connectGenes();
        $a2Genes = $a2->connectGenes();

        $count = 0;
        $countA1 = false;
        for ($i = $minInnov; $i <= $maxInnov; ++$i) {
            if (isset($a1Genes[$i]) && isset($a2Genes[$i])) {
                $count = 0;
            } elseif (!isset($a2Genes[$i])) {
                if (false === $countA1) {
                    $count = 1;
                    $countA1 = true;
                } else {
                    ++$count;
                }
            } else {
                if (true === $countA1) {
                    $count = 1;
                    $countA1 = false;
                } else {
                    ++$count;
                }
            }
        }

        return $count;
    }

    /**
     * Count how many disjoint genes exist between two agents.
     *
     * @param  AgentInterface $a1
     * @param  AgentInterface $a2
     *
     * @return int
     */
    public function disjointGenesNb(AgentInterface $a1, AgentInterface $a2): int
    {
        $maxInnov = max($a1->maxConnectInnovation(), $a2->maxConnectInnovation());

        $a1Genes = $a1->connectGenes();
        $a2Genes = $a2->connectGenes();

        $count = 0;
        for ($i = 1; $i <= $maxInnov; ++$i) {
            if (!isset($a1Genes[$i]) || !isset($a2Genes[$i])) {
                ++$count;
            }
        }

        return $count - $this->excessGenesNb($a1, $a2);
    }

    /**
     * Compute the average weight difference of two agents' matching genes.
     *
     * @param  AgentInterface $a1
     * @param  AgentInterface $a2
     *
     * @return float
     */
    public function avgWeightDifference(AgentInterface $a1, AgentInterface $a2): float
    {
        $maxInnov = max($a1->maxConnectInnovation(), $a2->maxConnectInnovation());
        $a1Genes = $a1->connectGenes();
        $a2Genes = $a2->connectGenes();

        $weightDiff = 0;
        $count = 0;
        for ($i = 1; $i <= $maxInnov; ++$i) {
            if (isset($a1Genes[$i]) && isset($a2Genes[$i])) {
                $weightDiff += abs($a1Genes[$i]->weight() - $a2Genes[$i]->weight());
                ++$count;
            }
        }

        return $count !== 0 ? ($weightDiff / $count) : 0.0;
    }

    /**
     * Count how many node matching genes of two agents have different activation functions.
     *
     * @param  AgentInterface $a1
     * @param  AgentInterface $a2
     *
     * @return int
     */
    public function activationFnDifference(AgentInterface $a1, AgentInterface $a2): int
    {
        $maxInnov = max($a1->maxNodeInnovation(), $a2->maxNodeInnovation());
        $a1Genes = $a1->nodeGenes();
        $a2Genes = $a2->nodeGenes();

        $count = 0;
        for ($i = 1; $i <= $maxInnov; ++$i) {
            if (isset($a1Genes[$i]) && isset($a2Genes[$i]) && $a1Genes[$i]->activationFunction() !== $a2Genes[$i]->activationFunction()) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Count how many node matching genes of two agents have different aggregation functions.
     *
     * @param  AgentInterface $a1
     * @param  AgentInterface $a2
     *
     * @return int
     */
    public function aggregationFnDifference(AgentInterface $a1, AgentInterface $a2): int
    {
        $maxInnov = max($a1->maxNodeInnovation(), $a2->maxNodeInnovation());
        $a1Genes = $a1->nodeGenes();
        $a2Genes = $a2->nodeGenes();

        $count = 0;
        for ($i = 1; $i <= $maxInnov; ++$i) {
            if (isset($a1Genes[$i]) && isset($a2Genes[$i]) && $a1Genes[$i]->aggregationFunction() !== $a2Genes[$i]->aggregationFunction()) {
                ++$count;
            }
        }

        return $count;
    }
}
