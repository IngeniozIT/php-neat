<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

use IngeniozIT\Math\ActivationFunction;

class Genome
{
	protected $inputNodes = [];
	protected $outputNodes = [];
	protected $hiddenNodes = [];
	protected $nodes = [];

	protected $activationFunctions;
	protected $aggregationFunctions;

	public function __construct(array $activationFunctions, array $aggregationFunctions)
	{
		$this->activationFunctions = $activationFunctions;
		$this->aggregationFunctions = $aggregationFunctions;
	}

	// genome building

	protected function addNode(int $id, int $activationFunction, int $aggregationFunction, bool $active)
	{
		if (!isset($this->activationFunctions[$activationFunction])) {
			throw new \Exception('Unknown activation function '.$activationFunction);
		}
		if (!isset($this->aggregationFunctions[$aggregationFunction])) {
			throw new \Exception('Unknown aggregation function '.$aggregationFunction);
		}
		$this->nodes[$id] = [$activationFunction, $aggregationFunction, $active];
	}

	public function addInputNode(int $id, int $activationFunction, int $aggregationFunction, bool $active = true)
	{
		$this->inputNodes[] = $id;
		$this->addNode($id, $activationFunction, $aggregationFunction, $active);
	}

	public function addOutputNode(int $id, int $activationFunction, int $aggregationFunction)
	{
		$this->outputNodes[] = $id;
		$this->addNode($id, $activationFunction, $aggregationFunction, true);
	}

	public function addHiddenNode(int $id, int $activationFunction, int $aggregationFunction, bool $active = true)
	{
		$this->hiddenNodes[] = $id;
		$this->addNode($id, $activationFunction, $aggregationFunction, $active);
	}

	protected $connexions = [];
	protected $connexionNodes = [];

	public function addConnexionGene(int $connexionId, int $inId, int $outId, float $weight)
	{
		$this->connexions[$connexionId] = $weight;
		$this->connexionNodes[$inId][$outId] = $connexionId;
	}

	public function hasConnexion(int $connexionId): bool
	{
		return isset($this->connexions[$connexionId]);
	}

	public function checkConnexion(int $connexionId)
	{
		if (!$this->hasConnexion($connexionId)) {
			throw new \Exception('Connexion "'.$connexionId.'" does not exist.');
		}
	}

	public function hasNode(int $nodeId): bool
	{
		return isset($this->nodes[$nodeId]);
	}

	public function checkNode(int $nodeId)
	{
		if (!$this->hasNode($nodeId)) {
			throw new \Exception('Node "'.$nodeId.'" does not exist.');
		}
	}

	// genome mutations

	public function getConnexionWeight(int $connexionId): float
	{
		$this->checkConnexion($connexionId);
		return $this->connexions[$connexionId];
	}

	public function setConnexionWeight(int $connexionId, float $weight)
	{
		$this->checkConnexion($connexionId);
		$this->connexions[$connexionId] = $weight;
	}

	public function disableNode(int $nodeId)
	{
		$this->checkNode($nodeId);
		$this->nodes[$nodeId][2] = false;
	}

	public function enableNode(int $nodeId)
	{
		$this->checkNode($nodeId);
		$this->nodes[$nodeId][2] = true;
	}

	public function toggleNode(int $nodeId)
	{
		$this->checkNode($nodeId);
		$this->nodes[$nodeId][2] = !$this->nodes[$nodeId][2];
	}

	// activation

	public function activate(array $inputValues)
	{
		$activations = [];
		$pendingActivations = [];

		// Activate input nodes
		foreach ($inputValues as $i => $value) {
			// Deactivated node
			if (!$this->nodes[$this->inputNodes[$i]][2]) {
				continue;
			}
			foreach ($this->connexionNodes[$this->inputNodes[$i]] ?? [] as $outNode => $connexionNode) {
				if (!$this->nodes[$outNode][2]) {
					continue;
				}
				$activations[$outNode][$this->inputNodes[$i]] = $value * $this->connexions[$connexionNode];
				if (!empty($this->connexionNodes[$outNode]) && !in_array($outNode, $pendingActivations)) {
					$pendingActivations[] = $outNode;
				}
			}
		}

		// Forward propagate activations
		do {
			$newPendingActivations = [];

			foreach ($pendingActivations as $nodeId) {
				foreach ($this->connexionNodes[$nodeId] ?? [] as $outNode => $connexionNode) {
					$activations[$outNode][$nodeId] = call_user_func(
						$this->activationFunctions[$this->nodes[$nodeId][0]],
						call_user_func(
							$this->aggregationFunctions[$this->nodes[$nodeId][1]],
							$activations[$nodeId]
						)
					) * $this->connexions[$connexionNode];
					if (!empty($this->connexionNodes[$outNode]) && $this->nodes[$outNode][2]) {
						$newPendingActivations[] = $outNode;
					}
				}
			}

			$pendingActivations = $newPendingActivations;

		} while (!empty($pendingActivations));

		// Collect the activation of the output nodes.
		$outputs = [];
		foreach ($this->outputNodes as $outputNodeId) {
			$outputs[] = call_user_func(
				$this->activationFunctions[$this->nodes[$outputNodeId][0]],
				call_user_func(
					$this->aggregationFunctions[$this->nodes[$outputNodeId][1]],
					$activations[$outputNodeId] ?? []
				)
			);
		}

		return $outputs;
	}
}
