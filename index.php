<?php
require_once 'vendor/autoload.php';

use IngeniozIT\Neat\Algo\NeatFactory;
use IngeniozIT\Neat\Threshold\MaxThreshold;
use IngeniozIT\Neat\Algo\Interfaces\PoolInterface;
use IngeniozIT\Neat\Implementation\Speciation\OriginalSpeciation;
use IngeniozIT\Neat\Implementation\Mating\DefaultMating;
use IngeniozIT\Math\ActivationFunction;

function xorFitness(PoolInterface $pool)
{
    $fitnesses = [];

    $xor = [
        [[1, 0, 0], 0],
        [[1, 0, 1], 1],
        [[1, 1, 0], 1],
        [[1, 1, 1], 0],
    ];
    foreach ($pool->agents() as $i => $agent) {
        $fitness = 4;
        foreach ($xor as $x) {
            $fitness -= abs($agent->activate($x[0])[0] - $x[1]);
        }
        $fitness = ($fitness ** 2) / (4 ** 2);
        $agent->setFitness($fitness);
        $fitnesses[$i] = $fitness;
    }

    // foreach ($pool->getSpecies() as $speciesId => $agents) {
    //     echo '- ', $speciesId, ' => ', count($agents), PHP_EOL;
    // }
    echo 'Species : '.count($pool->getSpecies()), PHP_EOL;
    echo 'Min fitness : '.min($fitnesses), PHP_EOL;
    echo 'Max fitness : '.max($fitnesses), PHP_EOL;
    echo 'Avg fitness : '.(array_sum($fitnesses) / count($fitnesses)), PHP_EOL;
    echo PHP_EOL;
}

function boolFitness(PoolInterface $pool)
{
    $fitnesses = [];

    $xor = [
        [[1, 0, 0], [0, 0, 0]],
        [[1, 0, 1], [1, 0, 1]],
        [[1, 1, 0], [1, 0, 1]],
        [[1, 1, 1], [1, 1, 0]],
    ];
    foreach ($pool->agents() as $i => $agent) {
        $fitness = 0;
        foreach ($xor as $x) {
            $act = $agent->activate($x[0]);
            foreach ($act as $j => $val) {
                $fitness += abs($val - $x[1][$j]);
            }
        }
        $fitness = (12 - $fitness) ** 2;
        $agent->setFitness($fitness);
        $fitnesses[$i] = $fitness;
    }

    // foreach ($pool->getSpecies() as $speciesId => $agents) {
    //     echo '- ', $speciesId, ' => ', count($agents), PHP_EOL;
    // }
    echo 'Species : '.count($pool->getSpecies()), PHP_EOL;
    echo 'Min fitness : '.min($fitnesses), PHP_EOL;
    echo 'Max fitness : '.max($fitnesses), PHP_EOL;
    echo 'Avg fitness : '.(array_sum($fitnesses) / count($fitnesses)), PHP_EOL;
    echo PHP_EOL;
}

function customSigmoid(float $val): float
{
    return 1 / (1 + exp(-4.9 * $val));
}

// mt_srand(42);
$factory = new NeatFactory();
// $factory->setSpeciationFunction(new OriginalSpeciation(3, 1.0, 1.0, 0.4, 0.4, 0.4));
$factory->setActivationFunctions(['customSigmoid']);
$factory->setDefaultActivationFunction(['customSigmoid']);
// $factory->setActivationFunctions([[ActivationFunction::class, 'binaryStep']]);
// $factory->setDefaultActivationFunction([[ActivationFunction::class, 'binaryStep']]);
// $factory->setActivationFunctions(['customSigmoid', [ActivationFunction::class, 'binaryStep']]);
// $factory->setDefaultActivationFunction(['customSigmoid']);

$neat = $factory->createNeat(3, 1, 150, new MaxThreshold(0.9), 'xorFitness');
$neat->setMaxGenerations(100);

var_dump($neat->run());
echo $neat->pool()->champion();
