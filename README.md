# php-neat

A PHP implementation of the NeuroEvolution of Augmenting Topologies algorithm.

## Master branch status

[![Build Status](https://travis-ci.com/IngeniozIT/php-neat.svg?branch=master)](https://travis-ci.com/IngeniozIT/php-neat)
[![Code Coverage](https://codecov.io/gh/IngeniozIT/php-neat/branch/master/graph/badge.svg)](https://codecov.io/gh/IngeniozIT/php-neat)

## Library structure

### Namespace `IngeniozIT\Neat\Agents`

Contains classes related to the agents (the neural networks that compete with each other) used by the NEAT algoritm.

#### Genome

`Genome()` is an angent's neural network encoded as node genes and connection genes.

#### Agent

`Agent()` is an actual agent used by the algorithm.

#### AgentFactory

`AgentFactory()` is a factory used to instantiate classes in the `IngeniozIT\Neat\Agents` namespace.

### Namespace `IngeniozIT\Neat\Genotype`

Contains classes related to an agent's genotype (historical markings, node genes and connexion genes).

#### NodeGenotype

`NodeGenotype(int $innovId, int $type)` is a historical marking for a node gene.

#### NodeGene

`NodeGene(int $innovId, int $type, callable $aggregationFunction, callable $activationFunction)` is a node gene used by agents.

#### ConnectGenotype

`ConnectGenotype(int $innovId, int $inId, int $outId)` is a historical marking for a connection gene.

#### ConnectGene

`ConnectGene(int $innovId, int $inId, int $outId, float $weight, bool $disabled)` is a connection gene used by agents.

#### GenotypeFactory

`GenotypeFactory()` is a factory used to instantiate classes in the `IngeniozIT\Neat\Genotype` namespace.


### Namespace `IngeniozIT\Neat\Exceptions`

Contains all the exceptions that the library will use.

**Note :** all exceptions raised by this library will implement the `IngeniozIT\Neat\Exceptions\NeatExceptionInterface` interface.
