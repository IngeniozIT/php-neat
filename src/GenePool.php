<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

class GenePool
{
	protected $nodeId = 0;
	protected $connectId = 0;

	protected $nodeGenes = [];

	protected $inputGenes = [];
	protected $biasGenes = [];
	protected $outputGenes = [];
	protected $hiddenGenes = [];

	protected $connexionGenes = [];

	protected $connexionLinks = [];


	public function addInputGene()
	{
		++$this->nodeId;
		$this->nodeGenes[] = $this->nodeId;
		$this->inputGenes[] = $this->nodeId;
	}

	public function addBiasGene()
	{
		++$this->nodeId;
		$this->nodeGenes[] = $this->nodeId;
		$this->biasGenes[] = $this->nodeId;
	}

	public function addOutputGene()
	{
		++$this->nodeId;
		$this->nodeGenes[] = $this->nodeId;
		$this->outputGenes[] = $this->nodeId;
	}

	public function addHiddenGene()
	{
		++$this->nodeId;
		$this->nodeGenes[] = $this->nodeId;
		$this->hiddenGenes[] = $this->nodeId;
	}

	public function addConnexionGene(int $inId, int $outId)
	{
		++$this->connectId;
		$this->connexionGenes[$this->nodeId] = [$inId, $outId];
		$this->connexionLinks[$inId][$outId] = $this->nodeId;
	}

	public function getConnexionGeneId(int $inId, int $outId): int
	{
		if (!isset($this->connexionLinks[$inId][$outId])) {
			$this->addConnexionGene($inId, $outId);
		}

		return $this->connexionLinks[$inId][$outId];
	}
}
