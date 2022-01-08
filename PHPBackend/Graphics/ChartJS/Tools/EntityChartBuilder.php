<?php
namespace PHPBackend\Graphics\ChartJS\Tools;

use PHPBackend\Config;
use PHPBackend\DBEntity;
use PHPBackend\Graphics\ChartJS\ChartConfig;
use PHPBackend\Graphics\ChartJS\Chart;
use PHPBackend\Calendar\Month;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class EntityChartBuilder
{
    /**
     * Une collection des entites
     * @var DBEntity[]
     */
    protected $elements=array();
    
    /**
     * la configuration de l'application
     * @var Config
     */
    private $config;
    
    /**
     * un tableau cle => valeur des options
     * @var array
     */
    private $options;
    
    /**
     * @var Chart
     */
    private $chart;

    /**
     * constructeur d'initialisation
     * @param Config $config
     * @param array $options
     * @param array $elements
     */
    public function __construct(?Config $config=null, array $options = array(), array $elements = array())
    {
        $this->config=$config;
        $this->options = $options;
        $this->setElements($elements);
    }
    
    
    /**
     * recupere une option dans la collection des parametres
     * @param string $key
     * @return mixed|NULL
     */
    protected function getOption ($key) {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }
        return null;
    }
    
    /**
     * Ajout d'un parametre dans la collection des options
     * @param string $name
     * @param mixed $value
     */
    protected function addOption(string $name, $value) : void {
        $this->options[$name] = $value;
    }
    
    /**
     * @return \PHPBackend\Graphics\ChartJS\Chart
     */
    public function getChart() : ?Chart
    {
        if ($this->chart == null) {
            $this->refresh();
        }
        return $this->chart;
    }
    
    /**
     * Demande de regeneration de la configuration graphique du chart JS
     */
    public function refresh () : void {
        $this->chart = new Chart($this->doGenerate($this->config));
    }

    /**
     * @return multitype:\PHPBackend\DBEntity
     */
    public function getElements()
    {
        return $this->elements;
    }
    
    /**
     * @param multitype:\PHPBackend\DBEntity  $elements
     */
    public function setElements(array $elements) : void
    {
        $this->elements = $elements;
    }
    
    /**
     * Ajout d'un element
     * @param DBEntity $element
     */
    public function addElement ($element) : void {
        array_push($this->elements, $element);
    }
    
    /**
     * y a-t il aumoin un element??
     * @return bool
     */
    public function hasElement () : bool{
        return !empty($this->getElements());
    }
    
    /**
     * Revoie la collection des labels des dates dans l'initervale en parametre
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @return array
     */
    public function getDatesLabels (\DateTime $dateMin, \DateTime $dateMax) : array {
        $maxAbs = Month::countDates($dateMin, $dateMax);
        $labels = array();
        for ($i = 0; $i <= $maxAbs; $i++) {
            /**
             * @var \DateTime $date
             */
            $date = (clone $dateMin)->modify("+{$i} days");
            
            $labels [] = $date->format('d/m/Y');
        }
        
        return $labels;
    }
    
    /**
     * Methode utilitaire de gerneration du plan 2d
     * @param Config
     * @return ChartConfig
     */
    protected abstract function doGenerate (?Config $config) : ChartConfig;
    
}

