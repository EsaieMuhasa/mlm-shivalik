<?php

namespace Core\Shivalik\Entities;

use PHPBackend\Dao\DAOException;
use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 * retrait de l'arget sur une rubrique budgetaire.
 * Pour les rubrique liee aux comptes des membres, la transaction se fait vers le compte du dit membre.
 */
class Output extends DBEntity {

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string|null
     */
    private $description;

    /**
     * la rubrique toucher par ladite operation
     *
     * @var BudgetRubric
     */
    private  $rubric;

    /**
     * Get the value of amount
     *
     * @return  float
     */ 
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     *
     * @param  float  $amount
     */ 
    public function setAmount(?float $amount) : void
    {
        $this->amount = $amount;
    }

    /**
     * Get the value of description
     *
     * @return  string|null
     */ 
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string|null  $description
     */ 
    public function setDescription($description) : void
    {
        $this->description = $description;
    }

    /**
     * @return  BudgetRubric
     */ 
    public function getRubric() : ?BudgetRubric
    {
        return $this->rubric;
    }

    /**
     * @param  BudgetRubric  $rubric  la rubrique toucher par ladite operation
     */ 
    public function setRubric($rubric) : void
    {
        if($rubric == null || $rubric instanceof BudgetRubric){
            $this->rubric = $rubric;
        } else if(self::isInt($rubric)) {
            $this->rubric = new BudgetRubric(['id' => $rubric]);
        } else {
            throw new PHPBackendException('invalid arguement value in setRubric method');
        }
    }
}