<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents;

use IngeniozIT\Neat\Agents\Interfaces\GenomeInterface;
use IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface;
use IngeniozIT\Neat\Genotype\Interfaces\ConnectGeneInterface;
use IngeniozIT\Neat\Genotype\Interfaces\SensorNodeGeneInterface;
use IngeniozIT\Neat\Genotype\Interfaces\OutputNodeGeneInterface;
use IngeniozIT\Neat\Exceptions\RuntimeException;

class Genome implements GenomeInterface
{
    protected $nodeGenes = [];
    protected $connectGenes = [];

    protected $sensorNodes = [];
    protected $outputNodes = [];

    public function nodeGenes(): array
    {
        return $this->nodeGenes;
    }

    public function connectGenes(): array
    {
        return $this->connectGenes;
    }

    public function addNodeGene(NodeGeneInterface $nodeGene): void
    {
        $innovId = $nodeGene->innovId();

        if (isset($this->nodeGenes[$innovId])) {
            throw new RuntimeException("Node gene with innovation $innovId already exists.");
        }

        $this->nodeGenes[$innovId] = $nodeGene;

        if ($nodeGene->isSensor()) {
            $this->sensorNodes[$innovId] = true;
        } elseif ($nodeGene->isOutput()) {
            $this->outputNodes[$innovId] = true;
        }
    }

    public function addConnectGene(ConnectGeneInterface $connectGene): void
    {
        $innovId = $connectGene->innovId();
        $inId = $connectGene->inId();
        $outId = $connectGene->outId();

        if (isset($this->connectGenes[$innovId])) {
            throw new RuntimeException("Connect gene $innovId already exists.");
        }
        if (!isset($this->nodeGenes[$inId])) {
            throw new RuntimeException("Genome does not have In node gene $inId.");
        }
        if (!isset($this->nodeGenes[$outId])) {
            throw new RuntimeException("Genome does not have Out node gene $outId.");
        }

        $this->connectGenes[$innovId] = $connectGene;
    }

    public function activate(array $inputValues): array
    {
        // Initialize nodes
        $activations = [];
        $aggregationFunctions = [];
        $activationFunctions = [];
        $pendingActivations = [];
        foreach ($this->nodeGenes as $nodeGene) {
            $innovId = $nodeGene->innovId();
            $aggregationFunctions[$innovId] = $nodeGene->aggregationFunction();
            $activationFunctions[$innovId] = $nodeGene->activationFunction();
            if (isset($this->sensorNodes[$innovId])) {
                $activations[$innovId] = [array_shift($inputValues)];
                $pendingActivations[] = $innovId;
            } else {
                $activations[$innovId] = [];
            }
        }

        // Initialize connexions
        $connexions = [];
        foreach ($this->connectGenes as $connectGene) {
            if ($connectGene->isDisabled()) {
                continue;
            }
            $connexions[$connectGene->inId()][] = [
                $connectGene->outId(),
                $connectGene->weight()
            ];
        }

        // Activate neural network
        while (!empty($pendingActivations)) {
            $innovId = array_shift($pendingActivations);

            // Activation of the input node
            $inActivation = $activationFunctions[$innovId]($aggregationFunctions[$innovId]($activations[$innovId]));

            // Feed activation to all connexions
            foreach ($connexions[$innovId] ?? [] as $connexion) {
                $currentActivation = $activations[$connexion[0]][$innovId] ?? null;
                $activations[$connexion[0]][$innovId] = $connexion[1] * $inActivation;

                // Add out node to pending activations
                if (!in_array($connexion[0], $pendingActivations) 
                    && (                    null === $currentActivation 
                    || abs($currentActivation - $activations[$connexion[0]][$innovId]) > 0.01)
                ) {
                    $pendingActivations[] = $connexion[0];
                }
            }
        }

        // Get output activations
        $outputs = [];
        foreach ($this->outputNodes as $innovId => $true) {
            $outputs[] = $activationFunctions[$innovId](
                $aggregationFunctions[$innovId]($activations[$innovId])
            );
        }
        return $outputs;
    }

    public function toVector(int $nodeInnovation, int $connInnovation, array $aggregationFunctions, array $activationFunctions): array
    {
        $arr = [];

        // Nodes
        $aggrFnNb = count($aggregationFunctions);
        $actFnNb = count($activationFunctions);
        for ($i = 1; $i <= $nodeInnovation; ++$i) {
            if (!isset($this->nodeGenes[$i])) {
                // Node does not exist in this Genome
                foreach ($aggregationFunctions as $aggrFn) {
                    $arr[] = 0;
                }
                foreach ($activationFunctions as $actFn) {
                    $arr[] = 0;
                }
            } else {
                // Aggregation functions
                if ($aggrFnNb > 1) {
                    $aggrFnIndex = array_search($this->nodeGenes[$i]->aggregationFunction(), $aggregationFunctions);
                    if (false === $aggrFnIndex) {
                        throw new RuntimeException('Aggregation function not found.');
                    }
                    foreach ($aggregationFunctions as $aggrFnId => $aggrFn) {
                        $arr[] = $aggrFnId === $aggrFnIndex ? 1 : 0;
                    }
                }
                // Activation functions
                if ($actFnNb > 1) {
                    $actFnIndex = array_search($this->nodeGenes[$i]->activationFunction(), $activationFunctions);
                    if (false === $actFnIndex) {
                        throw new RuntimeException('Activation function not found.');
                    }
                    foreach ($activationFunctions as $actFnId => $actFn) {
                        $arr[] = $actFnId === $actFnIndex ? 1 : 0;
                    }
                }
            }
        }

        // Connexions
        for ($i = 1; $i <= $connInnovation; ++$i) {
            if (!isset($this->connectGenes[$i])) {
                $arr[] = 0;
                $arr[] = 0;
            } else {
                $arr[] = $this->connectGenes[$i]->isDisabled() ? 0 : 1;
                $arr[] = $this->connectGenes[$i]->weight();
            }
        }

        return $arr;
    }
}
