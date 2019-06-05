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

    public function run(): bool
    {
        $generation = 0;

        while (null === $this->maxGenerations || ++$generation <= $this->maxGenerations) {
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
        $this->speciate();
        $this->mate();

        return $this->thresholdMet();
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
        $this->selectionFunction()($pool);
        $this->matingFunction()($pool);
        foreach ($pool as $agent) {
            if (null === $agent->species()) {
                throw new RuntimeException('Mating : agent without species found.');
            }
        }
    }

    public function thresholdMet(): bool
    {
        return $this->threshold()->thresholdMet($this->pool);
    }
}
