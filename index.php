<?php
require_once 'vendor/autoload.php';

use IngeniozIT\Neat\Algo\NeatFactory;
use IngeniozIT\Neat\Threshold\MinThreshold;

function fnFunc($pool)
{
    foreach ($pool as $i => $agent) {
        $agent->setFitness($i + 1);
    }
}

$neat = (new NeatFactory())->createNeat(3, 1, 5, new MinThreshold(0.05), 'fnFunc');

$neat->run();
/*
$pool = new Pool(3, 1, 100);
$neat = new Neat($pool);
$neat->criterion(new MinThreshold(0.05));
$neat->setFitnessFunction('xorFitnessFunction');

$neat->maxGenerations(100);
$neat->terminateOnThresholdMet(false);
$neat->setInitializationFunction(new FullyConnectedInitialization());
$neat->setSelectionFunction(new OriginalSelection());
$neat->setMatingFunction(new OriginalMating());
$neat->setSpeciationFunction(new OriginalSpeciation());
$neat->setActivationFunctions([
    'fn1',
    'fn2',
    'fn3'
]);
$neat->setAggregationFunctions([
    'fn1',
    'fn2',
    'fn3'
]);
$neat->setDefaultActivationFunction('fn1');
$neat->setDefaultAggregationFunction('fn1');
*/
/*
use IngeniozIT\Neat\Algo\Neat;
use IngeniozIT\Neat\Threshold\MinThreshold;

$neat = (new NeatFactory())->createNeat(3, 1, 100, new MinThreshold(0.05), 'xorFitnessFunction');

$pool = new Pool(3, 1, 100);
$neat = new Neat($pool);
$neat->setCriterion(new MinThreshold(0.05));
$neat->setFitnessFunction('xorFitnessFunction');

$neat->maxGenerations(100);
$neat->terminateOnThresholdMet(false);
$neat->setInitializationFunction(new FullyConnectedInitialization());
$neat->setSelectionFunction(new OriginalSelection());
$neat->setMatingFunction(new OriginalMating());
$neat->setSpeciationFunction(new OriginalSpeciation());
$neat->setActivationFunctions([
    'fn1',
    'fn2',
    'fn3'
]);
$neat->setAggregationFunctions([
    'fn1',
    'fn2',
    'fn3'
]);
$neat->setDefaultActivationFunction('fn1');
$neat->setDefaultAggregationFunction('fn1');
*/



/**
 * Create an AgentInterface from two AgentInterface parents.
 *
 * @param  AgentInterface $parent1 The most fit parent.
 * @param  AgentInterface $parent2 The less fit parent.
 *
 * @return AgentInterface
 *//*
public function createAgentFromParents(AgentInterface $parent1, AgentInterface $parent2): AgentInterface
{
    list($nodeGenes, $connectGenes) = $this->getOffspringGenes($parent1, $parent2);

    return $this->createAgent($nodeGenes, $connectGenes);
}

/**
 * Create an GenomeInterface from two GenomeInterface parents.
 *
 * @param  GenomeInterface $parent1 The most fit parent.
 * @param  GenomeInterface $parent2 The less fit parent.
 *
 * @return GenomeInterface
 *//*
public function createGenomeFromParents(GenomeInterface $parent1, GenomeInterface $parent2): GenomeInterface
{
    list($nodeGenes, $connectGenes) = $this->getOffspringGenes($parent1, $parent2);

    return $this->createGenome($nodeGenes, $connectGenes);
}
/**
 * Create an AgentInterface from two AgentInterface parents.
 *
 * @param  AgentInterface $parent1 The most fit parent.
 * @param  AgentInterface $parent2 The less fit parent.
 *
 * @return AgentInterface
 *//*
public function createAgentFromParents(AgentInterface $parent1, AgentInterface $parent2): AgentInterface;

/**
 * Create an GenomeInterface from two GenomeInterface parents.
 *
 * @param  GenomeInterface $parent1 The most fit parent.
 * @param  GenomeInterface $parent2 The less fit parent.
 *
 * @return GenomeInterface
 *//*
public function createGenomeFromParents(GenomeInterface $parent1, GenomeInterface $parent2): GenomeInterface;

/**
 * Combine the genes from two parents.
 * This method does not mutate the genes, it just selects which genes an offspring will inherit as explained in part
 * 3.2 ("Tracking Genes through Historical Markings") and figure 4 of the original NEAT article.
 *
 * @link http://nn.cs.utexas.edu/downloads/papers/stanley.ec02.pdf
 *
 * @param  GenomeInterface $parent1 The most fit parent.
 * @param  GenomeInterface $parent2 The less fit parent.
 *
 * @return array [NodeGeneInterface[], ConnectGeneInterface[]]
 */
/*
protected function getOffspringGenes(GenomeInterface $parent1, GenomeInterface $parent2): array
{
    // Get parents connect genes
    $parent1ConnectGenes = $parent1->connectGenes();
    $parent2ConnectGenes = $parent2->connectGenes();
    $maxConnectInnovId = max(
        max(array_keys($parent1ConnectGenes)),
        max(array_keys($parent2ConnectGenes))
    );

    // Get parents node genes
    $maxNodeInnovId = 0;
    $mandatoryNodeGenes = [];
    $parent1NodeGenes = $parent1->nodeGenes();
    $parent2NodeGenes = $parent2->nodeGenes();
    foreach ($parent1NodeGenes as $innovId => $nodeGene) {
        $maxNodeInnovId = max($maxNodeInnovId, $innovId);
        if (!$nodeGene->isHidden()) {
            $mandatoryNodeGenes[$innovId] = true;
        }
    }
    foreach ($parent2NodeGenes as $connectGene) {
        $maxNodeInnovId = max($maxNodeInnovId, $nodeGene->innovId());
        if (!$nodeGene->isHidden()) {
            $mandatoryNodeGenes[$innovId] = true;
        }
    }

    // Select offspring connect genes and their corresponding node genes innovation ids
    $offspringConnectGenes = [];
    for ($i = 1; $i <= $maxConnectInnovId; ++$i) {
        if (!isset($parent1ConnectGenes[$i])) {
            // Do not inherit gene
            continue;
        }
        $offspringConnectGenes[$i] = clone (isset($parent2ConnectGenes[$i]) && rand() % 2) ?
            $parent2ConnectGenes[$i] :
            $parent1ConnectGenes[$i];
        $mandatoryNodeGenes[$offspringConnectGenes[$i]->inId()] = true;
        $mandatoryNodeGenes[$offspringConnectGenes[$i]->outId()] = true;
    }

    // Select node genes so each connect gene can be attached to an in and out node
    $offspringNodeGenes = [];
    foreach ($mandatoryNodeGenes as $innovId => $foo) {
        $offspringConnectGenes[$innovId] = clone (isset($parent2NodeGenes[$innovId]) && rand() % 2) ?
            $parent2NodeGenes[$innovId] :
            $parent1NodeGenes[$innovId];
    }

    return [$offspringNodeGenes, $offspringConnectGenes];
}*/