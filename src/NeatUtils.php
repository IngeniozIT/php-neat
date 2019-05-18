<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

use IngeniozIT\NEAT\Interfaces\GenePoolInterface;
use IngeniozIT\NEAT\NEAT;
use IngeniozIT\Math\Random;

class NeatUtils
{
    public static function initPartiallyConnected(NEAT &$neat)
    {
        self::initNotConnected($neat);

        $genomePool = $neat->pool();
        $genePool = $genomePool->genePool();
        $genomes = $genomePool->genomes();

        $inputGenes = $genePool->inputGenes();
        $outputGenes = $genePool->outputGenes();

        foreach ($genomes as &$genome) {
            $inId = $inputGenes[rand(0, count($inputGenes) - 1)];
            $outId = $outputGenes[rand(0, count($outputGenes) - 1)];
            $genome->addConnexion(
                $genePool->connexionGeneId($inId, $outId),
                $inId,
                $outId,
                Random::nrand(
                    $neat->getWeightInitializationMean(),
                    $neat->getWeightInitializationStdev()
                )
            );
        }
    }

    public static function initFullyConnected(NEAT &$neat)
    {
        self::initNotConnected($neat);

        $genomePool = $neat->pool();
        $genePool = $genomePool->genePool();
        $genomes = $genomePool->genomes();

        $inputGenes = $genePool->inputGenes();
        $outputGenes = $genePool->outputGenes();

        foreach ($inputGenes as $inId) {
            foreach ($outputGenes as $outId) {
                $connId = $genePool->connexionGeneId($inId, $outId);
                foreach ($genomes as &$genome) {
                    $genome->addConnexion(
                        $connId,
                        $inId,
                        $outId,
                        Random::nrand(
                            $neat->getWeightInitializationMean(),
                            $neat->getWeightInitializationStdev()
                        )
                    );
                }
            }
        }
    }

    public static function initNotConnected(NEAT &$neat)
    {
        $neat->validatePoolCreation();

        $genomePool = $neat->pool();
        $genePool = $genomePool->genePool();

        $genomeClass = $neat->getGenomeClass();

        // Gene pool
        $nbInputs = $neat->getNbInputs();
        for ($i = 1; $i <= $nbInputs; ++$i) {
            $genePool->addInputGene();
        }
        $nbOutputs = $neat->getNbOutputs();
        for ($i = 1; $i <= $nbOutputs; ++$i) {
            $genePool->addOutputGene();
        }

        // Genome pool
        $inputGenes = $genePool->inputGenes();
        $outputGenes = $genePool->outputGenes();
        $populationSize = $neat->getPopulationSize();
        for ($i = 1; $i <= $populationSize; ++$i) {
            $genome = new $genomeClass($neat->getActivationFunctions(), $neat->getAggregationFunctions());

            foreach ($inputGenes as $inId) {
                $genome->addinputNode($inId, 0, 0);
            }

            foreach ($outputGenes as $outId) {
                $genome->addOutputNode($outId, 0, 0);
            }

            $genomePool->addGenome($genome);
        }
    }

    public static function min(GenomePoolInterface $genomePool, float $threshold): bool
    {
        foreach ($genomePool->genomes() as $genome) {
            if ($genome->fitness() <= $threshold) {
                return true;
            }
        }

        return false;
    }

    public static function max(GenomePoolInterface $genomePool, float $threshold): bool
    {
        foreach ($genomePool->genomes() as $genome) {
            if ($genome->fitness() >= $threshold) {
                return true;
            }
        }

        return false;
    }
}
