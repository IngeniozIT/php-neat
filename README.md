# php-neat

A PHP implementation of the NeuroEvolution of Augmenting Topologies algorithm.

## Master branch status

[![Build Status](https://travis-ci.com/IngeniozIT/php-neat.svg?branch=master)](https://travis-ci.com/IngeniozIT/php-neat)
[![Code Coverage](https://codecov.io/gh/IngeniozIT/php-neat/branch/master/graph/badge.svg)](https://codecov.io/gh/IngeniozIT/php-neat)

## Library structure

### Namespace `IngeniozIT\Neat\Genotype`

This namespace contains classes related to an agent's genotype (node genes and connexion genes).

- `NodeGene(int $innovId, int $type, callable $aggregationFunction, callable $activationFunction)` : a node gene. Type can either be `NodeGenotype::TYPE_SENSOR` (sensor/input node), `NodeGenotype::TYPE_OUTPUT` (output node) or `NodeGenotype::TYPE_HIDDEN` (hidden node).
- `ConnectGene(int $innovId, int $inId, int $outId, float $weight, bool $disabled)` : a connexion gene.

- `NodeGenotype(int $innovId, int $type)` : not used by the agents *per se*, but used to store the node genes historical markings.
- `ConnectGenotype(int $innovId, int $inId, int $outId)` : not used by the agents *per se*, but used to store the connexion genes historical markings.