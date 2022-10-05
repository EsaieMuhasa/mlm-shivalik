<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

class SubConfigElement extends DBEntity {

    /**
     * @var float
     */
    private $percent;

    /**
     * @var BudgetRubric|null
     */
    private $rubric;

    /**
     * @var ConfigElement|null
     */
    private $config;

    /**
     * Get the value of percent
     *
     * @return  float
     */ 
    public function getPercent() : ? float
    {
        return $this->percent;
    }

    /**
     * Set the value of percent
     *
     * @param  float  $percent
     */ 
    public function setPercent($percent) : void
    {
        $this->percent = $percent;
    }


    /**
     * Get the value of rubric
     *
     * @return  BudgetRubric|null
     */ 
    public function getRubric() : ? BudgetRubric
    {
        return $this->rubric;
    }

    /**
     * Set the value of rubric
     *
     * @param  BudgetRubric|null  $rubric
     *
     * @return  self
     */ 
    public function setRubric($rubric) : void
    {
        if($rubric instanceof BudgetRubric || $rubric == null){
            $this->rubric = $rubric;
        } else if (self::isInt($rubric)) {
            $this->rubric = new BudgetRubric(['id' => $rubric]);
        } else {
            throw new PHPBackendException('invalide argument type in setRubric() : void method');
        }
    }

    /**
     * Get the value of config
     *
     * @return  ConfigElement|null
     */ 
    public function getConfig() : ?ConfigElement
    {
        return $this->config;
    }

    /**
     * Set the value of config
     *
     * @param  ConfigElement|null  $config
     */ 
    public function setConfig($config) : void
    {
        if ($config instanceof ConfigElement || $config == null) {
            $this->config = $config;
        } else if (self::isInt($config)) {
            $this->config = new ConfigElement(['id' => $config]);
        } else {
            throw new PHPBackendException('invalide argument type in setConfig method');
        }
    }
}