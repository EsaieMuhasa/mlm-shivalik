<?php

namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MHS
 *        
 */
class VirtualMoney extends DBEntity {

	/**
	 * le montant virtuele accoder
	 * @var number
	 */
	private $amount;
	
	/**
	 * montant virtuel accorder
	 * @var number
	 */
	private $expected;
	
	/**
	 * collection d'operations.
	 * pour chaque operation, une retrocession doit etre soustrait au montant attendue
	 * @var GradeMember[]
	 */
	private $debts = [];
	
	/**
	 * @var Office
	 */
	private $office;
	
	/**
	 * @var RequestVirtualMoney
	 */
	private $request;
	
	/**
	 * Le bonus generer par le montant virtuel
	 * @var OfficeBonus
	 */
	private $bonus;
	
	
	/**
     * @return OfficeBonus
     */
    public function getBonus() : ?OfficeBonus
    {
        return $this->bonus;
    }

    /**
     * @param OfficeBonus $bonus
     */
    public function setBonus($bonus)
    {
        if ($bonus == null || $bonus instanceof OfficeBonus) {
            $this->bonus = $bonus;
        } else if (self::isInt($bonus)) {
            $this->bonus = new OfficeBonus(array('id' => $bonus));
        } else {
            throw new PHPBackendException("invalid param value in setBonus () method");
        }
    }

    /**
	 * @return number
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @return Office
	 */
	public function getOffice() : ?Office{
		return $this->office;
	}

	/**
	 * @param number $amount
	 */
	public function setAmount($amount) : void {
		$this->amount = $amount;
	}

	/**
	 * @param Office $office
	 */
	public function setOffice($office) : void{
	    if ($office == null || $office instanceof Office) {
    		$this->office = $office;
	    }else if (self::isInt($office)) {
	        $this->office = new Office(array('id' => $office));
	    }else{
	        throw new PHPBackendException("Invalid param value in setOffice method");
	    }
	}
	
    /**
     * @return RequestVirtualMoney
     */
    public function getRequest() : ?RequestVirtualMoney
    {
        return $this->request;
    }

    /**
     * @param RequestVirtualMoney $request
     */
    public function setRequest($request): void
    {
        if ($request instanceof RequestVirtualMoney || $request == null) {
            $this->request = $request;
        }else if (self::isInt($request)) {
            $this->request = new RequestVirtualMoney(array('id' => $request));
        }else {
            throw new PHPBackendException("invalid value in setRequest() method");
        }
    }
    
    /**
     * @return number
     */
    public function getExpected()
    {
        return $this->expected;
    }

    /**
     * @param number $expected
     */
    public function setExpected($expected) : void
    {
        $this->expected = $expected;
    }
    
    /**
     * @return GradeMember[]
     */
    public function getDebts()
    {
        return $this->debts;
    }

    /**
     * @param GradeMember[]  $debts
     */
    public function setDebts($debts) : void 
    {
        $this->debts = $debts;
    }
    
    /**
     * @param GradeMember $gm
     */
    public function addDebt (GradeMember $gm) : void {
        $this->debts[] = $gm;
    }
    
    /**
     * Revoie le solde des operations qui ont toucher le montant attendue
     * @return int
     */
    public function getSoldDebts () : int {
        $sold = 0;
        foreach ($this->getDebts() as $debt) {//la dette touche uniquement l'adhession
            $sold += $debt->getMembership();
        }
        return $sold;
    }

}

