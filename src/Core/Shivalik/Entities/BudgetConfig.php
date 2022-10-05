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
}