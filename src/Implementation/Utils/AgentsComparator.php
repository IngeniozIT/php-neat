<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Implementation\Utils;

use IngeniozIT\Neat\Agents\Interfaces\AgentInterface;

class AgentsComparator
{
    protected $a1;
    protected $a2;

    protected $minCommonConnectInnovNb = null;
    protected $minCommonNodeInnovNb = null;
    protected $maxConnectInnovNb = null;
    protected $maxNodeInnovNb = null;
    protected $excessGenesNb = null;

    public function __construct(AgentInterface $a1, AgentInterface $a2)
    {
        $this->a1 = $a1;
        $this->a2 = $a2;
    }

    protected function computeInnovNbs(): void
    {
        $a1InnovNb = $this->a1->maxConnectInnovation();
        $a2InnovNb = $this->a2->maxConnectInnovation();
        $this->minCommonConnectInnovNb = min($a1InnovNb, $a2InnovNb);
        $this->maxConnectInnovNb = max($a1InnovNb, $a2InnovNb);

        $a1InnovNb = $this->a1->maxNodeInnovation();
        $a2InnovNb = $this->a2->maxNodeInnovation();
        $this->minCommonNodeInnovNb = min($a1InnovNb, $a2InnovNb);
        $this->maxNodeInnovNb = max($a1InnovNb, $a2InnovNb);
    }

    public function maxConnectNb(): int
    {
        return max($this->a1->connectGenesNb(), $this->a2->connectGenesNb());
    }

    public function maxNodeNb(): int
    {
        return max($this->a1->nodeGenesNb(), $this->a2->nodeGenesNb());
    }

    public function minCommonConnectInnovNb(): int
    {
        if (null === $this->minCommonConnectInnovNb) {
            $this->computeInnovNbs();
        }

        return $this->minCommonConnectInnovNb;
    }

    public function maxConnectInnovNb(): int
    {
        if (null === $this->maxConnectInnovNb) {
            $this->computeInnovNbs();
        }

        return $this->maxConnectInnovNb;
    }

    public function minCommonNodeInnovNb(): int
    {
        if (null === $this->minCommonNodeInnovNb) {
            $this->computeInnovNbs();
        }

        return $this->minCommonNodeInnovNb;
    }

    public function maxNodeInnovNb(): int
    {
        if (null === $this->maxNodeInnovNb) {
            $this->computeInnovNbs();
        }

        return $this->maxNodeInnovNb;
    }

    /**
     * Count how many excess genes exist between two agents.
     *
     * @return int
     */
    public function excessGenesNb(): int
    {
        if (null === $this->excessGenesNb) {
            $maxInnov = $this->maxConnectInnovNb();
            $minInnov = $this->minCommonConnectInnovNb();

            $a1Genes = $this->a1->connectGenes();
            $a2Genes = $this->a2->connectGenes();

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
            $this->excessGenesNb = $count;
        }

        return $this->excessGenesNb;
    }

    /**
     * Count how many disjoint genes exist between two agents.
     *
     * @return int
     */
    public function disjointGenesNb(): int
    {
        $maxInnov = $this->maxConnectInnovNb();

        $a1Genes = $this->a1->connectGenes();
        $a2Genes = $this->a2->connectGenes();

        $count = 0;
        for ($i = 1; $i <= $maxInnov; ++$i) {
            if (!isset($a1Genes[$i]) || !isset($a2Genes[$i])) {
                ++$count;
            }
        }

        return $count - $this->excessGenesNb($this->a1, $this->a2);
    }

    /**
     * Compute the average weight difference of two agents' matching genes.
     *
     * @return float
     */
    public function avgWeightDifference(): float
    {
        $maxInnov = $this->maxConnectInnovNb();
        $a1Genes = $this->a1->connectGenes();
        $a2Genes = $this->a2->connectGenes();

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
     * @return int
     */
    public function activationFnDifference(): int
    {
        $maxInnov = $this->maxNodeInnovNb();
        $minInnov = $this->minCommonNodeInnovNb();

        $a1Genes = $this->a1->nodeGenes();
        $a2Genes = $this->a2->nodeGenes();

        $count = 0;
        for ($i = $minInnov; $i <= $maxInnov; ++$i) {
            if (isset($a1Genes[$i]) && isset($a2Genes[$i]) && $a1Genes[$i]->activationFunction() !== $a2Genes[$i]->activationFunction()) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Count how many node matching genes of two agents have different aggregation functions.
     *
     * @return int
     */
    public function aggregationFnDifference(): int
    {
        $maxInnov = $this->maxNodeInnovNb();
        $minInnov = $this->minCommonNodeInnovNb();

        $a1Genes = $this->a1->nodeGenes();
        $a2Genes = $this->a2->nodeGenes();

        $count = 0;
        for ($i = $minInnov; $i <= $maxInnov; ++$i) {
            if (isset($a1Genes[$i]) && isset($a2Genes[$i]) && $a1Genes[$i]->aggregationFunction() !== $a2Genes[$i]->aggregationFunction()) {
                ++$count;
            }
        }

        return $count;
    }
}
