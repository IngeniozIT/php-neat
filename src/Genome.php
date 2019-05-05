<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

use IngeniozIT\NEAT\Interfaces\GenomeInterface;
use IngeniozIT\NEAT\Exceptions\GenomeException;
use IngeniozIT\Math\ActivationFunction;

class Genome implements GenomeInterface
{
    protected $inputNodes = [];
    protected $outputNodes = [];
    protected $hiddenNodes = [];
    protected $nodes = [];

    protected $activationFunctions;
    protected $aggregationFunctions;

    protected $connexions = [];
    protected $connexionNodes = [];

    protected $fitness = null;

    protected $species = null;

    /**
     * Constructor.
     *
     * @param  callable[] $activationFunctions  The list of possible activation functions.
     * @param  callable[] $aggregationFunctions The list of possible aggregation functions.
     * @return self
     */
    public function __construct(array $activationFunctions, array $aggregationFunctions)
    {
        $this->activationFunctions = $activationFunctions;
        $this->aggregationFunctions = $aggregationFunctions;
    }

    protected function addNode(int $id, int $activationFn, int $aggregationFn, bool $active): void
    {
        if (!isset($this->activationFunctions[$activationFn])) {
            throw new GenomeException('Unknown activation function '.$activationFn);
        }
        if (!isset($this->aggregationFunctions[$aggregationFn])) {
            throw new GenomeException('Unknown aggregation function '.$aggregationFn);
        }
        if ($this->hasNode($id)) {
            throw new GenomeException('Node "'.$id.'" already exists.');
        }
        $this->nodes[$id] = [$activationFn, $aggregationFn, $active];
    }

    /**
     * Add an input node to the genome.
     *
     * @param int  $id            The id of the node.
     * @param int  $activationFn  The index of the activation function of the node.
     * @param int  $aggregationFn The index of the aggregation function of the node.
     * @param bool $active        True if the node should be active, false otherwise.
     */
    public function addInputNode(int $id, int $activationFn, int $aggregationFn, bool $active = true): void
    {
        $this->addNode($id, $activationFn, $aggregationFn, $active);
        $this->inputNodes[] = $id;
    }

    /**
     * Add an output node to the genome.
     *
     * @param int $id            The id of the node.
     * @param int $activationFn  The index of the activation function of the node.
     * @param int $aggregationFn The index of the aggregation function of the node.
     */
    public function addOutputNode(int $id, int $activationFn, int $aggregationFn): void
    {
        $this->addNode($id, $activationFn, $aggregationFn, true);
        $this->outputNodes[] = $id;
    }

    /**
     * Add a hidden node to the genome.
     *
     * @param int  $id            The id of the node.
     * @param int  $activationFn  The index of the activation function of the node.
     * @param int  $aggregationFn The index of the aggregation function of the node.
     * @param bool $active        True if the node should be active, false otherwise.
     */
    public function addHiddenNode(int $id, int $activationFn, int $aggregationFn, bool $active = true): void
    {
        $this->addNode($id, $activationFn, $aggregationFn, $active);
        $this->hiddenNodes[] = $id;
    }

    /**
     * Add a connexion to the genome.
     *
     * @param int   $connexionId The id of the connexion gene.
     * @param int   $inId        The id of the node considered as input.
     * @param int   $outId       The id of the node considered as output.
     * @param float $weight      The strength of the connexion.
     */
    public function addConnexion(int $connexionId, int $inId, int $outId, float $weight): void
    {
        if ($this->hasConnexion($connexionId)) {
            throw new GenomeException('Connexion "'.$connexionId.'" already exists.');
        }
        $this->checkNode($inId);
        $this->checkNode($outId);
        $this->connexions[$connexionId] = $weight;
        $this->connexionNodes[$inId][$outId] = $connexionId;
    }

    /**
     * Check if the genome has a specific connexion.
     *
     * @param  int $connexionId The id of the connexion to check.
     * @return bool True if the genome has this connexion, false otherwise.
     */
    public function hasConnexion(int $connexionId): bool
    {
        return isset($this->connexions[$connexionId]);
    }

    /**
     * Check if the genome has a specific connexion gene and throw Exception if it does not.
     *
     * @param  int $connexionId The id of the connexion to check.
     * @throws \IngeniozIT\NEAT\Exceptions\GenomeException if the connexion gene does not exist.
     */
    public function checkConnexion(int $connexionId): void
    {
        if (!$this->hasConnexion($connexionId)) {
            throw new GenomeException('Connexion "'.$connexionId.'" does not exist.');
        }
    }

    /**
     * Check if the genome has a specific node.
     *
     * @param  int $nodeId The id of the node to check.
     * @return bool True if the genome has this node, false otherwise.
     */
    public function hasNode(int $nodeId): bool
    {
        return isset($this->nodes[$nodeId]);
    }

    /**
     * Check if the genome has a specific node and throw Exception if it does not.
     *
     * @param  int $nodeId The id of the node to check.
     * @throws \IngeniozIT\NEAT\Exceptions\GenomeException if the node gene does not exist.
     */
    public function checkNode(int $nodeId): void
    {
        if (!$this->hasNode($nodeId)) {
            throw new GenomeException('Node "'.$nodeId.'" does not exist.');
        }
    }

    /**
     * Get the weight of a specific connexion.
     *
     * @param  int $connexionId The id of the connexion.
     * @return float The weight of the connexion.
     */
    public function getConnexionWeight(int $connexionId): float
    {
        $this->checkConnexion($connexionId);
        return $this->connexions[$connexionId];
    }

    /**
     * Set the weight of a specific connexion.
     *
     * @param int   $connexionId The id of the connexion.
     * @param float $weight      The new weight of the connexion.
     */
    public function setConnexionWeight(int $connexionId, float $weight): void
    {
        $this->checkConnexion($connexionId);
        $this->connexions[$connexionId] = $weight;
    }

    /**
     * Disable a node.
     *
     * @param int $nodeId The node to disable.
     */
    public function disableNode(int $nodeId): void
    {
        $this->checkNode($nodeId);
        $this->nodes[$nodeId][2] = false;
    }

    /**
     * Enable a node.
     *
     * @param int $nodeId The node to enable.
     */
    public function enableNode(int $nodeId): void
    {
        $this->checkNode($nodeId);
        $this->nodes[$nodeId][2] = true;
    }

    /**
     * Toggle a node.
     * If the node was disabled, it will be enabled. If the node was enabled, it will be disabled.
     *
     * @param int $nodeId The node to toggle.
     */
    public function toggleNode(int $nodeId): void
    {
        $this->checkNode($nodeId);
        $this->nodes[$nodeId][2] = !$this->nodes[$nodeId][2];
    }

    /**
     * Feed the genome some input values and get its output.
     *
     * @param  float[] $inputValues The list of input values.
     * @return float[] The list of output values.
     */
    public function activate(array $inputValues): array
    {
        $activations = [];
        $pendingActivations = [];

        // Activate input nodes
        foreach ($inputValues as $i => $value) {
            // Deactivated node
            if (!$this->nodes[$this->inputNodes[$i]][2]) {
                continue;
            }
            foreach ($this->connexionNodes[$this->inputNodes[$i]] ?? [] as $outNode => $connexionNode) {
                if (!$this->nodes[$outNode][2]) {
                    continue;
                }
                $activations[$outNode][$this->inputNodes[$i]] = $value * $this->connexions[$connexionNode];
                if (!empty($this->connexionNodes[$outNode]) && !in_array($outNode, $pendingActivations)) {
                    $pendingActivations[] = $outNode;
                }
            }
        }

        // Forward propagate activations
        do {
            $newPendingActivations = [];

            foreach ($pendingActivations as $nodeId) {
                foreach ($this->connexionNodes[$nodeId] ?? [] as $outNode => $connexionNode) {
                    $activations[$outNode][$nodeId] = call_user_func(
                        $this->activationFunctions[$this->nodes[$nodeId][0]],
                        call_user_func(
                            $this->aggregationFunctions[$this->nodes[$nodeId][1]],
                            $activations[$nodeId]
                        )
                    ) * $this->connexions[$connexionNode];
                    if (!empty($this->connexionNodes[$outNode]) && $this->nodes[$outNode][2]) {
                        $newPendingActivations[] = $outNode;
                    }
                }
            }

            $pendingActivations = $newPendingActivations;
        } while (!empty($pendingActivations));

        // Collect the activation of the output nodes.
        $outputs = [];
        foreach ($this->outputNodes as $outputNodeId) {
            $outputs[] = call_user_func(
                $this->activationFunctions[$this->nodes[$outputNodeId][0]],
                call_user_func(
                    $this->aggregationFunctions[$this->nodes[$outputNodeId][1]],
                    $activations[$outputNodeId] ?? []
                )
            );
        }

        return $outputs;
    }

    /**
     * Get the vector representation of the genome.
     *
     * @return array The vector.
     */
    public function getVector(): array
    {
        return $this->connexions;
    }

    /**
     * Set the fitness of the genome.
     *
     * @param float $fitness The fitness.
     */
    public function setFitness(?float $fitness): void
    {
        $this->fitness = $fitness;
    }

    /**
     * Get the fitness of the genome.
     *
     * @return float|null The fitness of the genome.
     */
    public function getFitness(): ?float
    {
        return $this->fitness;
    }

    /**
     * Set the species of the genome.
     *
     * @param int|null $species The species.
     */
    public function setSpecies(?int $species): void
    {
        $this->species = $species;
    }

    /**
     * Get the species of the genome.
     *
     * @return int|null The species of the genome.
     */
    public function getSpecies(): ?int
    {
        return $this->species;
    }
}
