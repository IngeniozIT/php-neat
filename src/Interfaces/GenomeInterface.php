<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT\Interfaces;

interface GenomeInterface
{
    /**
     * Constructor.
     * @param callable[] $activationFunctions The list of possible activation functions.
     * @param callable[] $aggregationFunctions The list of possible aggregation functions.
     * @return self
     */
    public function __construct(array $activationFunctions, array $aggregationFunctions);

    /**
     * Add an input node to the genome.
     * @param int $id The id of the node.
     * @param int $activationFn The index of the activation function of the node.
     * @param int $aggregationFn The index of the aggregation function of the node.
     * @param bool $active True if the node should be active, false otherwise.
     */
    public function addInputNode(int $id, int $activationFn, int $aggregationFn, bool $active = true): void;

    /**
     * Add an output node to the genome.
     * @param int $id The id of the node.
     * @param int $activationFn The index of the activation function of the node.
     * @param int $aggregationFn The index of the aggregation function of the node.
     */
    public function addOutputNode(int $id, int $activationFn, int $aggregationFn): void;

    /**
     * Add a hidden node to the genome.
     * @param int $id The id of the node.
     * @param int $activationFn The index of the activation function of the node.
     * @param int $aggregationFn The index of the aggregation function of the node.
     * @param bool $active True if the node should be active, false otherwise.
     */
    public function addHiddenNode(int $id, int $activationFn, int $aggregationFn, bool $active = true): void;

    /**
     * Add a connexion to the genome.
     * @param int $connexionId The id of the connexion gene.
     * @param int $inId The id of the node considered as input.
     * @param int $outId The id of the node considered as output.
     * @param float $weight The strength of the connexion.
     */
    public function addConnexion(int $connexionId, int $inId, int $outId, float $weight): void;

    /**
     * Check if the genome has a specific connexion.
     * @param int $connexionId The id of the connexion to check.
     * @return bool True if the genome has this connexion, false otherwise.
     */
    public function hasConnexion(int $connexionId): bool;

    /**
     * Check if the genome has a specific connexion gene and throw Exception if it does not.
     * @param int $connexionId The id of the connexion to check.
     * @throws \IngeniozIT\NEAT\Exceptions\GenomeException if the connexion gene does not exist.
     */
    public function checkConnexion(int $connexionId): void;

    /**
     * Check if the genome has a specific node.
     * @param int $nodeId The id of the node to check.
     * @return bool True if the genome has this node, false otherwise.
     */
    public function hasNode(int $nodeId): bool;

    /**
     * Check if the genome has a specific node and throw Exception if it does not.
     * @param int $nodeId The id of the node to check.
     * @throws \IngeniozIT\NEAT\Exceptions\GenomeException if the node gene does not exist.
     */
    public function checkNode(int $nodeId): void;

    /**
     * Get the weight of a specific connexion.
     * @param int $connexionId The id of the connexion.
     * @return float The weight of the connexion.
     */
    public function getConnexionWeight(int $connexionId): float;

    /**
     * Set the weight of a specific connexion.
     * @param int $connexionId The id of the connexion.
     * @param float $weight The new weight of the connexion.
     */
    public function setConnexionWeight(int $connexionId, float $weight): void;

    /**
     * Disable a node.
     * @param int $nodeId The node to disable.
     */
    public function disableNode(int $nodeId): void;

    /**
     * Enable a node.
     * @param int $nodeId The node to enable.
     */
    public function enableNode(int $nodeId): void;

    /**
     * Toggle a node.
     * If the node was disabled, it will be enabled. If the node was enabled, it will be disabled.
     * @param int $nodeId The node to toggle.
     */
    public function toggleNode(int $nodeId): void;

    /**
     * Feed the genome some input values and get its output.
     * @param float[] $inputValues The list of input values.
     * @return float[] The list of output values.
     */
    public function activate(array $inputValues): array;

    public function getVector(): array;

    public function setFitness(float $fitness): void;

    public function getFitness(): ?float;

    public function setSpecies(?int $species): void;

    public function getSpecies(): ?int;
}
