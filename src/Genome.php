<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

use IngeniozIT\NEAT\Interfaces\GenomeInterface;
use IngeniozIT\NEAT\Exceptions\GenomeException;
use IngeniozIT\Math\ActivationFunction;

class Genome implements GenomeInterface
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

	protected function addNode(int $id, int $activationFunction, int $aggregationFunction, bool $active): void
	{
		if (!isset($this->activationFunctions[$activationFunction])) {
			throw new GenomeException('Unknown activation function '.$activationFunction);
		}
		if (!isset($this->aggregationFunctions[$aggregationFunction])) {
			throw new GenomeException('Unknown aggregation function '.$aggregationFunction);
		}
		if ($this->hasNode($id)) {
			throw new GenomeException('Node "'.$id.'" already exists.');
		}
		$this->nodes[$id] = [$activationFunction, $aggregationFunction, $active];
	}

	public function addInputNode(int $id, int $activationFunction, int $aggregationFunction, bool $active = true): void
	{
		$this->addNode($id, $activationFunction, $aggregationFunction, $active);
		$this->inputNodes[] = $id;
	}

	public function addOutputNode(int $id, int $activationFunction, int $aggregationFunction): void
	{
		$this->addNode($id, $activationFunction, $aggregationFunction, true);
		$this->outputNodes[] = $id;
	}

	public function addHiddenNode(int $id, int $activationFunction, int $aggregationFunction, bool $active = true): void
	{
		$this->addNode($id, $activationFunction, $aggregationFunction, $active);
		$this->hiddenNodes[] = $id;
	}

	protected $connexions = [];
	protected $connexionNodes = [];

	public function addConnexion(int $connexionId, int $inId, int $outId, float $weight): void
	{
		if ($this->hasConnexion($connexionId)) {
			throw new GenomeException('Connexion "'.$connexionId.'" already exists.');
		}
		$this->checkNode($inId);
		$this->checkNode($outId);
		$this->connexions[$connexionId] = $weight;
		$this->connexionNodes[$inId][$outId] = $connexionId;
	}

	public function hasConnexion(int $connexionId): bool
	{
		return isset($this->connexions[$connexionId]);
	}

	public function checkConnexion(int $connexionId): void
	{
		if (!$this->hasConnexion($connexionId)) {
			throw new GenomeException('Connexion "'.$connexionId.'" does not exist.');
		}
	}

	public function hasNode(int $nodeId): bool
	{
		return isset($this->nodes[$nodeId]);
	}

	public function checkNode(int $nodeId): void
	{
		if (!$this->hasNode($nodeId)) {
			throw new GenomeException('Node "'.$nodeId.'" does not exist.');
		}
	}

	// genome mutations

	public function getConnexionWeight(int $connexionId): float
	{
		$this->checkConnexion($connexionId);
		return $this->connexions[$connexionId];
	}

	public function setConnexionWeight(int $connexionId, float $weight): void
	{
		$this->checkConnexion($connexionId);
		$this->connexions[$connexionId] = $weight;
	}

	public function disableNode(int $nodeId): void
	{
		$this->checkNode($nodeId);
		$this->nodes[$nodeId][2] = false;
	}

	public function enableNode(int $nodeId): void
	{
		$this->checkNode($nodeId);
		$this->nodes[$nodeId][2] = true;
	}

	public function toggleNode(int $nodeId): void
	{
		$this->checkNode($nodeId);
		$this->nodes[$nodeId][2] = !$this->nodes[$nodeId][2];
	}

	// activation

	public function activate(array $inputValues): array
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
