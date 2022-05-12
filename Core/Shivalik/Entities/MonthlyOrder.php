<?php
namespace Core\Shivalik\Entities;


/**
 *
 * @author Esaie MUHASA
 *        
 */
class MonthlyOrder extends Operation {
    /**
     * date d'invalidation des commmandes d'un mois
     * @var \DateTime
     */
    private $disabilityDate;
    
    /**
     * Montant disponibe
     * @var float
     */
    private $available;
    
    /**
     * Montant deja utiliser
     * @var float
     */
    private $used;
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Operation::setAmount()
     */
    public function setAmount($amount): void {
        parent::setAmount($amount);
        if($this->getUsed() != null)
            $this->setAvailable($this->getAmount() - $this->getUsed());
        else 
            $this->setAvailable($amount);
    }

    /**
     * @return \DateTime
     */
    public function getDisabilityDate () : ?\DateTime {
        return $this->disabilityDate;
    }

    /**
     * @return number
     */
    public function getAvailable () : float {
        return $this->available;
    }

    /**
     * @param \DateTime $disabilityDate
     */
    public function setDisabilityDate ($disabilityDate) : void{
        $this->disabilityDate = $this->hydrateDate($disabilityDate);
    }

    /**
     * @param number $available
     */
    protected function setAvailable ($available) : void{
        $this->available = $available;
    }
    /**
     * @return number
     */
    public function getUsed() : ?float{
        return $this->used;
    }

    /**
     * @param number $used
     */
    public function setUsed($used) : void {
        if($used === null){
            $used = 0.0;
        }
        
        $this->used = $used;
        if($this->getAmount() != null)
            $this->setAvailable($this->getAmount() - $used);
    }
    
}

