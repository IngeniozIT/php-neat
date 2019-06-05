<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Threshold\Interfaces;

use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Agents\Interfaces\AgentInterface;

interface ThresholdInterface
{
    public function thresholdMet(PoolInterface $pool): bool;
    public function sort(AgentInterface $agent1, AgentInterface $agent2): int;
}
