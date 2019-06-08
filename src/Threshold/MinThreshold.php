<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Threshold;

use IngeniozIT\Neat\Threshold\Interfaces\ThresholdInterface;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Agents\Interfaces\AgentInterface;

class MinThreshold implements ThresholdInterface
{
    protected $threshold;

    public function __construct(float $threshold)
    {
        $this->threshold = $threshold;
    }

    public function thresholdMet(PoolInterface $pool): bool
    {
        foreach ($pool->agents() as $agent) {
            if ($agent->fitness() <= $this->threshold) {
                return true;
            }
        }

        return false;
    }

    public function sort(AgentInterface $agent1, AgentInterface $agent2): int
    {
        return $agent1->fitness() <=> $agent2->fitness();
    }
}
