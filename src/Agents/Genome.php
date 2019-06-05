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
    /**
     * @var NodeGeneInterface[]
     */
    protected $nodeGenes = [];

    /**
     * @var int
     */
    protected $maxNodeInnovNb = 0;

    /**
     * @var ConnectGeneInterface[]
     */
    protected $connectGenes = [];

    /**
     * @var int
     */
    protected $maxConnectInnovNb = 0;

    /**
     * @var array
     */
    protected $sensorNodes = [];

    /**
     * @var array
     */
    protected $outputNodes = [];

    /**
     * Get the genome's node genes.
     *
     * @return NodeGeneInterface[]
     */
    public function nodeGenes(): array
    {
        return $this->nodeGenes;
    }

    /**
     * Get the maximum node gene innovation number.
     *
     * @return int
     */
    public function maxNodeInnovation(): int
    {
        return $this->maxNodeInnovNb;
    }

    /**
     * Get the genome's connection genes.
     *
     * @return ConnectGeneInterface[]
     */
    public function connectGenes(): array
    {
        return $this->connectGenes;
    }

    /**
     * Get the maximum connection gene innovation number.
     *
     * @return int
     */
    public function maxConnectInnovation(): int
    {
        return $this->maxConnectInnovNb;
    }

    /**
     * Add a node gene to the genome.
     *
     * @param NodeGeneInterface $nodeGene [description]
     * @throws RuntimeException If the genome already has a node gene with this innovation number.
     */
    public function addNodeGene(NodeGeneInterface $nodeGene): void
    {
        $innovNb = $nodeGene->innovNb();

        if (isset($this->nodeGenes[$innovNb])) {
            throw new RuntimeException("Node gene with innovation $innovNb already exists.");
        }

        $this->nodeGenes[$innovNb] = $nodeGene;

        if ($nodeGene->isSensor()) {
            $this->sensorNodes[$innovNb] = true;
        } elseif ($nodeGene->isOutput()) {
            $this->outputNodes[$innovNb] = true;
        }

        if ($innovNb > $this->maxNodeInnovNb) {
            $this->maxNodeInnovNb = $innovNb;
        } else {
            ksort($this->nodeGenes);
            ksort($this->sensorNodes);
            ksort($this->outputNodes);
        }
    }

    /**
     * Add a connection gene to the genome.
     *
     * @param ConnectGeneInterface $nodeGene [description]
     * @throws RuntimeException If the genome already has a connection gene with this innovation number.
     * @throws RuntimeException If the genome is missing the input or output node.
     */
    public function addConnectGene(ConnectGeneInterface $connectGene): void
    {
        $innovNb = $connectGene->innovNb();
        $inId = $connectGene->inId();
        $outId = $connectGene->outId();

        if (isset($this->connectGenes[$innovNb])) {
            throw new RuntimeException("Connect gene $innovNb already exists.");
        }
        if (!isset($this->nodeGenes[$inId])) {
            throw new RuntimeException("Genome does not have In node gene $inId.");
        }
        if (!isset($this->nodeGenes[$outId])) {
            throw new RuntimeException("Genome does not have Out node gene $outId.");
        }

        $this->connectGenes[$innovNb] = $connectGene;

        if ($innovNb > $this->maxConnectInnovNb) {
            $this->maxConnectInnovNb = $innovNb;
        } else {
            ksort($this->connectGenes);
        }
    }

    /**
     * Activate the neural network.
     *
     * @param  float[] $inputValues A list of input values.
     *
     * @return float[] A list of output values.
     */
    public function activate(array $inputValues): array
    {
        // Initialization
        $activations = [];
        $connexions = [];
        $pendingActivations = [];
        $aggregationFunctions = [];
        $activationFunctions = [];
        foreach ($this->connectGenes as $connectGene) {
            // Do not process disabled connection genes
            if ($connectGene->isDisabled()) {
                continue;
            }

            $inId = $connectGene->inId();
            $outId = $connectGene->outId();

            // Save connexion
            $connexions[$inId][] = [
                $outId,
                $connectGene->weight()
            ];

            // Initialize activation of in and out nodes
            $activations[$inId] = [];
            $activations[$outId] = [];
        }
        // Add initial sensor nodes activation
        foreach ($this->sensorNodes as $innovId => $foo) {
            $activations[$innovId] = [array_shift($inputValues) ?? 0];
            $pendingActivations[] = $innovId;
        }
        // Add output nodes in case they were not linked
        foreach ($this->outputNodes as $innovId => $foo) {
            $activations[$innovId] = [];
        }
        // Save activation and aggregation functions
        foreach ($activations as $innovId => $foo) {
            $activationFunctions[$innovId] = $this->nodeGenes[$innovId]->activationFunction();
            $aggregationFunctions[$innovId] = $this->nodeGenes[$innovId]->aggregationFunction();
        }

        // Activate neural network
        while (!empty($pendingActivations)) {
            $innovId = array_shift($pendingActivations);

            // Activation of input node
            $inActivation = $activationFunctions[$innovId]($aggregationFunctions[$innovId]($activations[$innovId]));

            // Activate connexions
            foreach ($connexions[$innovId] ?? [] as $connexion) {
                $currentActivation = $activations[$connexion[0]][$innovId] ?? null;
                $activations[$connexion[0]][$innovId] = $connexion[1] * $inActivation;

                // Add out node to pending activations
                if (
                    !\in_array($connexion[0], $pendingActivations) &&
                    (
                        null === $currentActivation ||
                        abs($currentActivation - $activations[$connexion[0]][$innovId]) > 0.01
                    )
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

    /**
     * Get a vector representation of the genome.
     *
     * @param  int $nodeInnovId The node global innovation number.
     * @param  int $connInnovId The connection global innovation number.
     * @param  callable[] $aggrFns The list of aggregation functions used amongst all genomes.
     * @param  callable[] $actFns The list of activation functions used amongst all genomes.
     *
     * @return float[] A vector representing the genome.
     * @throws RuntimeException If the genome has an aggregation function that is not present in $aggrFns.
     * @throws RuntimeException If the genome has an activation function that is not present in $actFns.
     */
    public function toVector(int $nodeInnovId, int $connInnovId, array $actFns, array $aggrFns): array
    {
        $arr = [];

        // Nodes
        $aggrFnNb = count($aggrFns);
        $actFnNb = count($actFns);
        for ($i = 1; $i <= $nodeInnovId; ++$i) {
            if (!isset($this->nodeGenes[$i])) {
                // Node does not exist in this Genome
                if ($aggrFnNb > 1) {
                    foreach ($aggrFns as $aggrFn) {
                        $arr[] = 0;
                    }
                }
                if ($actFnNb > 1) {
                    foreach ($actFns as $actFn) {
                        $arr[] = 0;
                    }
                }
            } else {
                // Aggregation functions
                $aggrFnIndex = array_search($this->nodeGenes[$i]->aggregationFunction(), $aggrFns);
                if (false === $aggrFnIndex) {
                    throw new RuntimeException('Aggregation function not found.');
                }
                if ($aggrFnNb > 1) {
                    foreach ($aggrFns as $aggrFnId => $aggrFn) {
                        $arr[] = $aggrFnId === $aggrFnIndex ? 1 : 0;
                    }
                }
                // Activation functions
                $actFnIndex = array_search($this->nodeGenes[$i]->activationFunction(), $actFns);
                if (false === $actFnIndex) {
                    throw new RuntimeException('Activation function not found.');
                }
                if ($actFnNb > 1) {
                    foreach ($actFns as $actFnId => $actFn) {
                        $arr[] = $actFnId === $actFnIndex ? 1 : 0;
                    }
                }
            }
        }

        // Connexions
        for ($i = 1; $i <= $connInnovId; ++$i) {
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
