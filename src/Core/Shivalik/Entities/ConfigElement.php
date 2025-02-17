<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 * element de la repartition globale du budget
 */
class ConfigElement extends DBEntity {

    /**
     * @var float
     */
    private $percent;

    /**
     * @var BudgetConfig|null
     */
    private $config;

    /**
     * @var BudgetRubric|null
     */
    private $rubric;

    /**
     * element de repatiton de la sous configuration
     *
     * @var SubConfigElement[]
     */
    private $elements = [];


    /**
     * Get the value of percent
     *
     * @return  float
     */ 
    public function getPercent() : ?float
    {
        return $this->percent;
    }

    /**
     * Set the value of percent
     *
     * @param  float  $percent
     */ 
    public function setPercent(float $percent) : void
    {
        $this->percent = $percent;
    }

    /**
     * Get the value of config
     *
     * @return  BudgetConfig|null
     */ 
    public function getConfig() : ?BudgetConfig
    {
        return $this->config;
    }

    /**
     * Set the value of config
     *
     * @param  BudgetConfig|null  $config
     */ 
    public function setConfig($config) : void
    {
        if ($config instanceof BudgetConfig || $config == null) {
            $this->config = $config;
        } else if (self::isInt($config)) {
            $this->config = new BudgetConfig(['id' => $config]);
        } else {
            new PHPBackendException('invalide arguement type in setConfig method');
        }
    }

    /**
     * Get the value of rubric
     *
     * @return  BudgetRubric|null
     */ 
    public function getRubric() :  ? BudgetRubric
    {
        return $this->rubric;
    }

    /**
     * Set the value of rubric
     *
     * @param  BudgetRubric|null  $rubric
     */ 
    public function setRubric($rubric) : void
    {
        if($rubric instanceof BudgetRubric || $rubric == null) {
            $this->rubric = $rubric;
        } else if (self::isInt($rubric)) {
            $this->rubric = new  BudgetRubric(['id' => $rubric]);
        } else {
            throw new PHPBackendException('invalide arguement type in setRubric method');
        }
    }

    /**
     * ajout d'un elememnt, comme sous configuration de la configuration globale
     *
     * @param SubConfigElement $element
     * @return self
     */
    public function addElement (SubConfigElement $element) : self {
        $this->elements[] = $element;
        return $this;
    }

    /**
     * renvoie la somme  des pourcentages des element constitualt la rubrique du budget gobale
     *
     * @return float
     */
    public function getSumOfElements () : float {
        $sum = 0;
        foreach ($this->elements as $element) {
            $sum += $element->getPercent();
        }
        return $sum;
    }
}