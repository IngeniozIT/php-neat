# Neat

A PHP implementation of the NeuroEvolution of Augmenting Topologies (NEAT) algorithm.

The goal of this library is to be as customizable as possible, so it can be used for practical purposes as well as for research purposes.

[![Build Status](https://travis-ci.com/IngeniozIT/php-neat.svg?branch=master)](https://travis-ci.com/IngeniozIT/php-neat)
[![Code Coverage](https://codecov.io/gh/IngeniozIT/php-neat/branch/master/graph/badge.svg)](https://codecov.io/gh/IngeniozIT/php-neat)

## Table of Contents

* [Installation](#installation)
* [Usage](#usage)
* [Implementation specificities](#implementation-specificities)
    * [Node genes](#node-genes)
    * [Exceptions](#exceptions)
* [Library structure](#library-structure)
* [Useful links](#useful-links)

## Installation

The library is not on Packagist yet.
In your `composer.json`, add :
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/IngeniozIT/php-neat"
        }
    ],
    "require": {
        "ingenioz-it/neat": "*"
    }
}
```

## Usage

This library is still in development. Do not use it yet.

A wiki with explanations on how to customize the algorithm will be created soon.

## Implementation specificities

### Node genes

In the original article, only connection genes were used during the mating process. This implementation also uses "node" genes, which allows to also mutate the aggregation functions and activation functions of the nodes the agents use.

In order to optimize the speciation process, the algorithm will not encode the activation function or aggregation function if there is only one of them. If only one activation function **and** aggregation function is given, the node genes will not be encoded at all.

### Exceptions

All exceptions raised implement the `IngeniozIT\Neat\Exceptions\NeatExceptionInterface` interface, which makes it easier to catch exceptions that come from the library :

```php
try {
    // NEAT code
} catch (\IngeniozIT\Neat\Exceptions\NeatExceptionInterface) {
    // Something bad happened ...
}
```
## Library structure

### `IngeniozIT\Neat\Agents`

This namespace contains classes related to the agents (the neural networks that compete with each other) used by the NEAT algoritm.

- `Genome` is an angent's neural network encoded as node genes and connection genes.
- `Agent` is an actual agent used by the algorithm.
- `AgentFactory` is a factory used to instantiate classes in the `IngeniozIT\Neat\Agents` namespace.

### `IngeniozIT\Neat\Genotype`

This namespace contains classes related to an agent's genotype (historical markings, node genes and connexion genes).

- `NodeGenotype` is a historical marking for a node gene.
- `NodeGene` is a node gene used by agents.
- `ConnectGenotype` is a historical marking for a connection gene.
- `ConnectGene` is a connection gene used by agents.
- `GenotypeFactory` is a factory used to instantiate classes in the `IngeniozIT\Neat\Genotype` namespace.


### `IngeniozIT\Neat\Exceptions`

This namespace contains all the exceptions that the library will throw.

## Useful links

- [Evolving Neural Networks through Augmenting Topologies](http://nn.cs.utexas.edu/downloads/papers/stanley.ec02.pdf) (the original NEAT article)
