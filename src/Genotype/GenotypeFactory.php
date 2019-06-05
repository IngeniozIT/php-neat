<?php
declare(strict_types = 1);

namespace IngeniozIT\Neat\Genotype;

use IngeniozIT\Neat\Genotype\Interfaces\GenotypeFactoryInterface;
use IngeniozIT\Neat\Genotype\Interfaces\NodeGenotypeInterface;
use IngeniozIT\Neat\Genotype\Interfaces\NodeGeneInterface;
use IngeniozIT\Neat\Genotype\NodeGenotype;
use IngeniozIT\Neat\Genotype\NodeGene;
use IngeniozIT\Neat\Genotype\Interfaces\ConnectGenotypeInterface;
use IngeniozIT\Neat\Genotype\Interfaces\ConnectGeneInterface;
use IngeniozIT\Neat\Genotype\ConnectGenotype;
use IngeniozIT\Neat\Genotype\ConnectGene;

class GenotypeFactory implements GenotypeFactoryInterface
{
    /**
     * The current maximum node innovation number.
     *
     * @var int
     * @internal
     */
    protected $currentGlobalNodeInnovNb = 0;

    /**
     * The current maximum connection innovation number.
     *
     * @var int
     * @internal
     */
    protected $currentGlobalConnectInnovNb = 0;

    /**
     * Create a NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     * @param  int $type Node type. NodeGenotype::TYPE_SENSOR, NodeGenotype::TYPE_OUTPUT or NodeGenotype::TYPE_HIDDEN.
     *
     * @return NodeGenotypeInterface
     */
    public function createNodeGenotype(int $type, ?int $innovNb = null): NodeGenotypeInterface
    {
        if (null === $innovNb) {
            $innovNb = ++$this->currentGlobalNodeInnovNb;
        } else {
            $this->currentGlobalNodeInnovNb = max($this->currentGlobalNodeInnovNb, $innovNb);
        }
        return new NodeGenotype($innovNb, $type);
    }

    /**
     * Create a sensor NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     *
     * @return NodeGenotypeInterface
     */
    public function createSensorNodeGenotype(?int $innovNb = null): NodeGenotypeInterface
    {
        return $this->createNodeGenotype(NodeGenotype::TYPE_SENSOR, $innovNb);
    }

    /**
     * Create an output NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     *
     * @return NodeGenotypeInterface
     */
    public function createOutputNodeGenotype(?int $innovNb = null): NodeGenotypeInterface
    {
        return $this->createNodeGenotype(NodeGenotype::TYPE_OUTPUT, $innovNb);
    }

    /**
     * Create hidden NodeGenotypeInterface.
     *
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     *
     * @return NodeGenotypeInterface
     */
    public function createHiddenNodeGenotype(?int $innovNb = null): NodeGenotypeInterface
    {
        return $this->createNodeGenotype(NodeGenotype::TYPE_HIDDEN, $innovNb);
    }

    /**
     * Create a NodeGeneInterface.
     *
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     * @param  int $type Node type. NodeGenotype::TYPE_SENSOR, NodeGenotype::TYPE_OUTPUT or NodeGenotype::TYPE_HIDDEN.
     * @param  callable $activationFunction
     * @param  callable $aggregationFunction
     *
     * @return NodeGeneInterface [description]
     */
    public function createNodeGene(
        int $type,
        callable $activationFunction,
        callable $aggregationFunction,
        ?int $innovNb = null
    ): NodeGeneInterface
    {
        if (null === $innovNb) {
            $innovNb = ++$this->currentGlobalNodeInnovNb;
        } else {
            $this->currentGlobalNodeInnovNb = max($this->currentGlobalNodeInnovNb, $innovNb);
        }
        return new NodeGene($innovNb, $type, $activationFunction, $aggregationFunction);
    }

    /**
     * Create a NodeGeneInterface from a NodeGenotypeInterface.
     *
     * @param  NodeGenotypeInterface $nodeGenotype
     * @param  callable $activationFunction
     * @param  callable $aggregationFunction
     *
     * @return NodeGeneInterface
     */
    public function createNodeGeneFromNodeGenotype(
        NodeGenotypeInterface $nodeGenotype,
        callable $activationFunction,
        callable $aggregationFunction): NodeGeneInterface
    {
        return $this->createNodeGene(
            $nodeGenotype->type(),
            $activationFunction,
            $aggregationFunction,
            $nodeGenotype->innovNb()
        );
    }

    /**
     * Create a ConnectGenotypeInterface.
     *
     * @param int $inId Innovation number of the input node.
     * @param int $outId Innovation number of the output node.
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     *
     * @return ConnectGenotypeInterface
     */
    public function createConnectGenotype(int $inId, int $outId, ?int $innovNb = null): ConnectGenotypeInterface
    {
        if (null === $innovNb) {
            $innovNb = ++$this->currentGlobalConnectInnovNb;
        } else {
            $this->currentGlobalConnectInnovNb = max($this->currentGlobalConnectInnovNb, $innovNb);
        }
        return new ConnectGenotype($innovNb, $inId, $outId);
    }

    /**
     * Create a ConnectGeneInterface.
     *
     * @param int $inId Innovation number of the input node.
     * @param int $outId Innovation number of the output node.
     * @param float $weight Initial connection weight.
     * @param bool $disabled True if the connection is disabled, false otherwise.
     * @param  int|null $innovNb Innovation number. If null is given a new innovation number will be given.
     *
     * @return ConnectGeneInterface
     */
    public function createConnectGene(
        int $inId,
        int $outId,
        float $weight,
        bool $disabled = false,
        ?int $innovNb = null
    ): ConnectGeneInterface
    {
       if (null === $innovNb) {
           $innovNb = ++$this->currentGlobalConnectInnovNb;
       } else {
           $this->currentGlobalConnectInnovNb = max($this->currentGlobalConnectInnovNb, $innovNb);
       }
       return new ConnectGene($innovNb, $inId, $outId, $weight, $disabled);
    }

    /**
     * Create a ConnectGeneInterface from a ConnectGenotypeInterface.
     *
     * @param  ConnectGenotypeInterface $connectGenotype
     * @param float $weight Initial connection weight.
     * @param bool $disabled True if the connection is disabled, false otherwise.
     *
     * @return ConnectGeneInterface
     */
    public function createConnectGeneFromConnectGenotype(
        ConnectGenotypeInterface $connectGenotype,
        float $weight,
        bool $disabled = false
    ): ConnectGeneInterface
    {
        return $this->createConnectGene(
            $connectGenotype->inId(),
            $connectGenotype->outId(),
            $weight,
            $disabled,
            $connectGenotype->innovNb()
        );
    }
}
