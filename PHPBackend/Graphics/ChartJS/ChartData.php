<?php
namespace PHPBackend\Graphics\ChartJS;

use PHPBackend\Serialisation\JSONSerialize;

/**
 *
 * @author Esaie MHS
 *        
 */
class ChartData
{
    
    /**
     * Collection des configuration des graphiques
     * @var ChartDataset[]
     */
    private $datasets = [];
    
    /**
     * Collection des label sur l'axe de X
     * @var string
     */
    private $labels = [];
    
    use JSONSerialize;

    /**
     * constructeur d'initialisation
     * @param array $datasets
     * @param array $labels
     */
    public function __construct(array $datasets = [], array $labels = [])
    {
        $this->setDatasets($datasets);
        $this->setLabels($labels);
    }
    
    /**
     * @return multitype:\PHPBackend\Graphics\ChartJS\ChartDataset 
     */
    public function getDatasets() : array
    {
        return $this->datasets;
    }
    
    /**
     * Ajout d'une configuration graphique a a la configuration graphique
     * @param ChartDataset $dataset
     */
    public function addDataset (ChartDataset $dataset) : void {
        foreach ($this->datasets as $ds) {
            if ($ds == $dataset) {
                return;
            }
        }
        
        $this->datasets[] = $dataset;
    }

    /**
     * @return string
     */
    public function getLabels() : array
    {
        return $this->labels;
    }

    /**
     * @param multitype:\PHPBackend\Graphics\ChartJS\ChartDataset  $datasets
     */
    public function setDatasets($datasets) : void
    {
        $this->datasets = $datasets;
    }

    /**
     * @param string $labels
     */
    public function setLabels($labels) : void
    {
        $this->labels = $labels;
    }

}

