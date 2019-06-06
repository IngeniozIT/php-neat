<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Algo;

use IngeniozIT\Neat\Algo\Interfaces\NeatInterface;
use IngeniozIT\Neat\Exceptions\RuntimeException;

class Neat extends NeatConfig implements NeatInterface
{
    protected $pool;
    protected $currentGeneration = 0;
    protected $maxGenerations = null;

    public function setMaxGenerations(?int $maxGenerations): void
    {
        $this->maxGenerations = $maxGenerations;
    }

    public function run(): bool
    {
        $generation = 0;

        while (null === $this->maxGenerations || ++$generation <= $this->maxGenerations) {
            echo 'GENERATION ', $generation, ' ', $this->pool->maxNodeInnovation(), ' ', $this->pool->maxConnectInnovation(), PHP_EOL;
            if ($this->runOnce()) {
                return true;
            }
        }

        return false;
    }

    public function runOnce(): bool
    {
        ++$this->currentGeneration;

        $this->evaluate();
        if ($this->thresholdMet()) {
            return true;
        }
        $this->speciate();
        $this->mate();
        return false;
    }

    public function evaluate(): void
    {
        echo __FUNCTION__, PHP_EOL;
        $pool = $this->pool();
        foreach ($pool as $agent) {
            $agent->setFitness(null);
        }
        $this->fitnessFunction()($pool);
        foreach ($pool as $agent) {
            if (null === $agent->fitness()) {
                throw new RuntimeException('Evaluation : agent without fitness found.');
            }
        }
        $pool->sort($this->threshold());
    }

    public function speciate(): void
    {
        echo __FUNCTION__, PHP_EOL;
        $pool = $this->pool();
        $this->speciationFunction()($pool);
        foreach ($pool as $agent) {
            if (null === $agent->species()) {
                throw new RuntimeException('Speciation : agent without species found.');
            }
        }
    }

    public function mate(): void
    {
        echo __FUNCTION__, PHP_EOL;
        $pool = $this->pool();
        $this->matingFunction()($pool);
        if (count($pool) < $pool->populationSize()) {
            throw new RuntimeException("Population size should be ".$pool->populationSize().", ".count($pool)." instead.");
        }
    }

    public function thresholdMet(): bool
    {
        echo __FUNCTION__, PHP_EOL;
        return $this->threshold()->thresholdMet($this->pool());
    }
}
