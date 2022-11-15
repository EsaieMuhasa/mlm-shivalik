<?php

namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;

/**
 * configuration de relartition du budget
 */
class BudgetConfig extends DBEntity {

    /**
     * est-ce toujours utilisable??
     *
     * @var boolean
     */
    private $available = false;

    /**
     * collection des elements de repartiton dela configuration
     *
     * @var ConfigElement[]
     */
    private $elements = [];

    /**
     * Get est-ce toujours utilisable??
     *
     * @return  boolean
     */ 
    public function getAvailable() : bool
    {
        return $this->available;
    }

    /**
     * @param  int|boolean
     */ 
    public function setAvailable ($available) : void
    {
        $this->available = self::isTrue($available);
    }

    /**
     * renvoie la liste des elements de configuration
     *
     * @return ConfigElement[]
     */
    public function getElements () : array {
        return $this->elements;
    }

    /**
     * Ajout d'un element de configuration de la repartition du budget
     *
     * @param ConfigElement $element
     * @return self
     */
    public function addElement (ConfigElement $element) : self{
        $this->elements[] = $element;
        return $this;
    }

    /**
     * Renvoie la sommes des pourcents des elements de la configuration
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