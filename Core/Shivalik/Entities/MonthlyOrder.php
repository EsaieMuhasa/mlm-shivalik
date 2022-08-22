<?php
namespace Core\Shivalik\Entities;


use PHPBackend\PHPBackendException;

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
     * montant manue.
     * Cette attribut a plus d'importance dans le cas d'integration des fiches dans le systeme
     * (sortie du systeme manue de gestion de stock au systeme informatiser)
     * @var number
     */
    private $manualAmount = 0;
    
    /**
     * @var Office
     */
    private $office;
    
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
     * @return float
     */
    public function getAvailable () : float {
        if ($this->manualAmount != 0) {
            return $this->manualAmount;
        }
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
     * @return float
     */
    public function getUsed() : ?float{
        if ($this->manualAmount != 0) {
            return $this->manualAmount;
        }
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
    
    /**
     * @return number
     */
    public function getManualAmount() {
        return $this->manualAmount;
    }

    /**
     * @param number $manualAmount
     */
    public function setManualAmount($manualAmount) : void {
        $this->manualAmount = $manualAmount;
    }
    
    /**
     * @return \Core\Shivalik\Entities\Office
     */
    public function getOffice () : ?Office{
        return $this->office;
    }

    /**
     * @param \Core\Shivalik\Entities\Office|int $office
     */
    public function setOffice ($office) : void {
        if ($office == null || $office instanceof Office) {
            $this->office = $office;
        } else if (self::isInt($office)) {
            $this->office = new Office(['id' => $office]);
        } else {
            throw new PHPBackendException("invalid arguement in setOffice() method of ". get_class($this). " class");
        }
    }

}

