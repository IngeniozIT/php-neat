<?php
declare(strict_types = 1);

use IngeniozIT\Math\ActivationFunction;

return [
	'nb_inputs' => 3,
	'nb_outputs' => 1,

	'population_size' => 10,
	'max_generations' => 10,

	'fitness_criterion' => 'max',
	'fitness_threshold' => 0.5,
	'fitness_function' => ['NeatTest', 'xorFitnessFunction'],

	// Activation functions

	'activation_fn_default' => [ActivationFunction::class, 'sigmoid'],
	'activation_fn_list' => [
		[ActivationFunction::class, 'identity'],
		[ActivationFunction::class, 'binaryStep'],
		[ActivationFunction::class, 'sigmoid'],
		[ActivationFunction::class, 'tanh'],
		[ActivationFunction::class, 'relu'],
		[ActivationFunction::class, 'leakyRelu'],
		[ActivationFunction::class, 'gaussian'],
	],

	// Aggregation functions

	'aggregation_fn_default' => 'array_sum',
	'aggregation_fn_list' => [
		'array_sum',
		'array_product',
	],

	// Weights

	'weight_init_mean' => 0,
	'weight_init_stdev' => 1,
	'weight_min_value' => -10,
	'weight_max_value' => 10,

	// Mutations

	'mut_interagent_rate' => 'default',

	'mut_activation_fn_rate' => 'default',
	'mut_aggregation_fn_rate' => 'default',
	'mut_add_node' => 'default',
	'mut_add_conn' => 'default',
	'mut_remove_conn' => 'default',
	'mut_change_weight' => 'default',
	'mut_reverse_weight' => 'default',
	'mut_replace_weight' => 'default',
	'mut_activate_node' => 'default',
	'mut_deactivate_node' => 'default',
];
