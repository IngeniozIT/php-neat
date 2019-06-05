<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Agents\Interfaces;

use IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface;
use IngeniozIT\Neat\Genotype\Interfaces\ConnectGeneInterface;

interface GenomeInterface
{
    /**
     * Get the genome's node genes.
     *
     * @return NodeGeneInterface[]
     */
    public function nodeGenes(): array;

    /**
     * Get the maximum node gene innovation number.
     *
     * @return int
     */
    public function maxNodeInnovation(): int;

    /**
     * Get the genome's connection genes.
     *
     * @return ConnectGeneInterface[]
     */
    public function connectGenes(): array;

    /**
     * Get the maximum connection gene innovation number.
     *
     * @return int
     */
    public function maxConnectInnovation(): int;

    /**
     * Add a node gene to the genome.
     *
     * @param NodeGeneInterface $nodeGene [description]
     * @throws RuntimeException If the genome already has a node gene with this innovation number.
     */
    public function addNodeGene(NodeGeneInterface $nodeGene): void;

    /**
     * Add a connection gene to the genome.
     *
     * @param ConnectGeneInterface $nodeGene [description]
     * @throws RuntimeException If the genome already has a connection gene with this innovation number.
     * @throws RuntimeException If the genome is missing the input or output node.
     */
    public function addConnectGene(ConnectGeneInterface $connectGene): void;

    /**
     * Activate the neural network.
     *
     * @param  float[] $inputValues A list of input values.
     *
     * @return float[] A list of output values.
     */
    public function activate(array $inputValues): array;

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
    public function toVector(int $nodeInnovId, int $connInnovId, array $aggrFns, array $actFns): array;
}
