<?php
require_once 'vendor/autoload.php';

use IngeniozIT\Neat\Algo\NeatFactory;
use IngeniozIT\Neat\Threshold\MinThreshold;

function fnFunc()
{

}

$neat = (new NeatFactory())->createNeat(3, 1, 100, new MinThreshold(0.05), 'fnFunc');

print_r($neat);

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
