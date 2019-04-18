# php-neat

A PHP implementation of the NeuroEvolution of Augmenting Topologies algorithm.

### Master branch status

[![Build Status](https://travis-ci.com/IngeniozIT/php-neat.svg?branch=master)](https://travis-ci.com/IngeniozIT/php-neat)
[![Code Coverage](https://codecov.io/gh/IngeniozIT/php-neat/branch/master/graph/badge.svg)](https://codecov.io/gh/IngeniozIT/php-neat)

### What does this library contains ?

This library contains :

- `Genome` : ANNs (Artificial Neural Networks) you can build from scratch.
- `GenePool` : a container for topological innovations (node genes and connexion genes) and their respective innovation numbers.
- `GenomePool` : a container for the genomes and their respective species.
- `NEAT` : executes the logic of the NEAT algorithm depending on the configuration it is given.