<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

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
     * @return Office
     */
    public function getOffice() : ?Office
    {
        return $this->office;
    }

    /**
     * @return Withdrawal[]
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
     * @param Office $office
     */
    public function setOffice($office) : void 
    {
        if ($office == null || $office instanceof Office){
            $this->office = $office;
        }elseif ($this->isInt($office)) {
            $this->office = new Office(array('id' => $office));
        }else{
            throw new PHPBackendException("invalid value in param of method setOffice");
        }
    }

    /**
     * @param multitype:Withdrawal  $withdrawals
     */
    public function setWithdrawals(array $withdrawals) : void
    {
        $this->withdrawals = $withdrawals;
    }

}

