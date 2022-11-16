<?php 

namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

class BudgetRubric extends DBEntity {

    /**
     * @var string
     */
    private $label;
    
    /**
     * @var string
     */
    private $description;

    /**
     * @var Member
     */
    private $owner;

     /**
     * @var RubricCategory|null
     */
    private $category;

    //resultat calculs de montants recu
    /**
     * resultat du calcul pour la repartition globale
     *
     * @var float
     */
    private $globalPart = 0;

    /**
     * resultat de calculs, pour le sous-rubriques budgetaires
     *
     * @var float
     */
    private $specificPart = 0;
    //==

    /**
     * somme des montants deja retirer, pour ladite rubrique
     *
     * @var float
     */
    private $sumOutlays = 0;


    /**
     * Get the value of label
     *
     * @return  string
     */ 
    public function getLabel() : ?string
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @param  string  $label
     */ 
    public function setLabel(?string $label) : void
    {
        $this->label = $label;
    }

    /**
     * Get the value of description
     *
     * @return  string
     */ 
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string  $description
     */ 
    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }

    /**
     * Get the value of owner
     *
     * @return  Member
     */ 
    public function getOwner() : ?Member
    {
        return $this->owner;
    }

    /**
     * Set the value of owner
     *
     * @param  Member|int  $owner
     */ 
    public function setOwner($owner) : void
    {
        if($owner instanceof Member || $owner == null) {
            $this->owner = $owner;
        } else if (self::isInt($owner)) {
            $this->owner = new Member(['id' => $owner]);
        } else {
            throw new PHPBackendException('invalide value in parametrer of setMember method');
        }
    }

    /**
     * Get the value of category
     *
     * @return  RubricCategory|null
     */ 
    public function getCategory() : ?RubricCategory
    {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @param  RubricCategory|null|int  $category
     */ 
    public function setCategory($category) : void
    {
        if ($category instanceof RubricCategory || $category == null) {
            $this->category = $category;
        } else if(self::isInt($category)) {
            $this->category = new RubricCategory(['id' => $category]);
        } else {
            throw new PHPBackendException('invalid arguement type in setCategory method');
        }
    }

    /**
     * Get resultat du calcul pour la repartition globale
     *
     * @return  float
     */ 
    public function getGlobalPart() : ?float
    {
        if ($this->globalPart === null) {
            return 0.0;
        }
        return $this->globalPart;
    }

    /**
     * Set resultat du calcul pour la repartition globale
     *
     * @param  float  $globalPart  resultat du calcul pour la repartition globale
     */ 
    public function setGlobalPart(?float $globalPart) : void
    {
        $this->globalPart = $globalPart;
    }

    /**
     * Get resultat de calculs, pour le sous-rubriques budgetaires
     *
     * @return  float
     */ 
    public function getSpecificPart() : ?float
    {
        if ($this->specificPart === null) {
            return 0.0;
        }
        return $this->specificPart;
    }

    /**
     * Set resultat de calculs, pour le sous-rubriques budgetaires
     *
     * @param  float  $specificPart  resultat de calculs, pour le sous-rubriques budgetaires
     */ 
    public function setSpecificPart(?float $specificPart) : void
    {
        $this->specificPart = $specificPart;
    }

    /**
     * Get somme des montants deja retirer, pour ladite rubrique
     *
     * @return  float
     */ 
    public function getSumOutlays() : ?float
    {
        if ($this->sumOutlays === null) {
            return 0.0;
        }
        return $this->sumOutlays;
    }

    /**
     * Set somme des montants deja retirer, pour ladite rubrique
     *
     * @param  float  $sumOutlays  somme des montants deja retirer, pour ladite rubrique
     */ 
    public function setSumOutlays(?float $sumOutlays) : void
    {
        $this->sumOutlays = $sumOutlays;
    }

    public function getAvailable () : float {
        $amount = $this->getSpecificPart() + $this->getGlobalPart() - $this->getSumOutlays();
        return $amount;
    }
}