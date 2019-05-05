<?php
declare(strict_types = 1);

namespace IngeniozIT\NEAT;

use IngeniozIT\NEAT\Interfaces\GenePoolInterface;
use IngeniozIT\NEAT\Exceptions\GenePoolException;

class GenePool implements GenePoolInterface
{
    protected $nodeId = -1;
    protected $connectId = -1;

    protected $nodeGenes = [];

    protected $inputGenes = [];
    protected $outputGenes = [];
    protected $hiddenGenes = [];

    protected $connexionGenes = [];

    protected $connexionLinks = [];

    public function addInputGene(): void
    {
        ++$this->nodeId;
        $this->nodeGenes[$this->nodeId] = self::NODE_INPUT;
        $this->inputGenes[] = $this->nodeId;
    }

    public function inputGenes(): array
    {
        return $this->inputGenes;
    }

    public function addOutputGene(): void
    {
        ++$this->nodeId;
        $this->nodeGenes[$this->nodeId] = self::NODE_OUTPUT;
        $this->outputGenes[] = $this->nodeId;
    }

    public function outputGenes(): array
    {
        return $this->outputGenes;
    }

    public function addHiddenGene(): void
    {
        ++$this->nodeId;
        $this->nodeGenes[$this->nodeId] = self::NODE_HIDDEN;
        $this->hiddenGenes[] = $this->nodeId;
    }

    public function hiddenGenes(): array
    {
        return $this->hiddenGenes;
    }

    public function nodeGenes(): array
    {
        return $this->nodeGenes;
    }

    public function nodeGeneExists(int $nodeId, int $nodeType = null): bool
    {
        return isset($this->nodeGenes[$nodeId]) &&
            (null === $nodeType ? true : $this->nodeGenes[$nodeId] === $nodeType);
    }

    protected function validConnexion(int $inId, int $outId)
    {
        if (!$this->nodeGeneExists($inId)) {
            throw new GenePoolException('Node gene '.$inId.' does not exist');
        }
        if (!$this->nodeGeneExists($outId)) {
            throw new GenePoolException('Node gene '.$outId.' does not exist');
        }
    }

    public function addConnexionGene(int $inId, int $outId): void
    {
        $this->validConnexion($inId, $outId);
        if (isset($this->connexionLinks[$inId][$outId])) {
            throw new GenePoolException('Connexion gene between '.$inId.' and '.$outId.' already exists');
        }
        ++$this->connectId;
        $this->connexionGenes[$this->connectId] = [$inId, $outId];
        $this->connexionLinks[$inId][$outId] = $this->connectId;
    }

    public function connexionGeneId(int $inId, int $outId): int
    {
        if (!isset($this->connexionLinks[$inId][$outId])) {
            $this->addConnexionGene($inId, $outId);
        }

        return $this->connexionLinks[$inId][$outId];
    }

    public function connexionGenes(): array
    {
        return $this->connexionGenes;
    }
}
