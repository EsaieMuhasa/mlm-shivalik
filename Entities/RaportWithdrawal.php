<?php
namespace Entities;

use Library\DBEntity;
use Library\LibException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class RaportWithdrawal extends DBEntity
{
    /**
     * @var Office
     */
    private $office;
    
    /**
     * @var Withdrawal[]
     */
    private $withdrawals = [];
    
    /**
     * @return \Entities\Office
     */
    public function getOffice() : ?Office
    {
        return $this->office;
    }

    /**
     * @return \Entities\Withdrawal[]
     */
    public function getWithdrawals() 
    {
        return $this->withdrawals;
    }
    
    /**
     * calcul du sold envoyer pour le raport
     * @return number
     */
    public function getSold () {
        $sold = 0;
        foreach ($this->withdrawals as $withd) {
            $sold += $withd->getAmount();
        }
        return $sold;
    }

    /**
     * @param \Entities\Office $office
     */
    public function setOffice($office) : void 
    {
        if ($office == null || $office instanceof Office){
            $this->office = $office;
        }elseif ($this->isInt($office)) {
            $this->office = new Office(array('id' => $office));
        }else{
            throw new LibException("invalid value in param of method setOffice");
        }
    }

    /**
     * @param multitype:\Entities\Withdrawal  $withdrawals
     */
    public function setWithdrawals(array $withdrawals) : void
    {
        $this->withdrawals = $withdrawals;
    }

}

