<?php

namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 * @author Esaie Muhasa
 * Cette entitee represente la requette que peuvent envoyer les investiseur pour demander des nouveau forfait.
 * le forfait est quant a elle representer par la classe VirtualMoney
 */
class RequestVirtualMoney extends DBEntity {
	
	/**
	 * @var float
	 * @deprecated   pour des raisons de changement de la logique de gestion des vituels cette attribut est obsolette 
	 * ainsi que sont accesseur et son mitateur.
	 */
	private $amount = 0;

	/**
	 * montant prevue pour l'afilisation
	 * @var float
	 */
	private $affiliation = 0;

	/**
	 * montant prevue pour l'achat de produit
	 * @var float
	 */
	private $product = 0;
	
	/**
	 * @var Office
	 */
	private $office;
	
	/**
	 * @var VirtualMoney
	 */
	private $response;

	    
    /**
     * @var Withdrawal[]
     */
    private $withdrawals = [];

	/**
	 * le nombre d'operation de matching qui font reference au rapport
	 * @var int
	 */
	private $withdrawalsCount = 0;
	
    /**
     * @return Withdrawal[]
     */
    public function getWithdrawals() 
    {
        return $this->withdrawals;
    }

    /**
     * @param Withdrawal[]  $withdrawals
     */
    public function setWithdrawals(array $withdrawals) : void
    {
        $this->withdrawals = $withdrawals;
		$product = 0;
		foreach ($withdrawals as $w) {
			$product += $w->getAmount();
		}
		$this->product = $product;
		$this->withdrawalsCount = count($withdrawals);
    }
	
	/**
	 * @return float
	 * @deprecated pour des raison de separation des virtules (afiliation et achat produit)
	 */
	public function getAmount() : ?float{
		return $this->amount;
	}
	
	/**
	 * @return Office
	 */
	public function getOffice() : ?Office {
		return $this->office;
	}
	
	/**
	 * @return VirtualMoney 
	 */
	public function getResponse() : ?VirtualMoney{
		return $this->response;
	}
	
	/**
	 * @param float $amount
	 * @deprecated pour des raison de separation des virtules (afiliation et achat produit)
	 */
	public function setAmount($amount) : void{
		$this->amount = $amount;
	}

	/**
	 * @param Office $office
	 */
	public function setOffice($office) : void {
		
		if ($office == null || $office instanceof Office) {
			$this->office = $office;
		}else if (self::isInt($office)) {
			$this->office = new Office(array('id' => $office));
		}else {
		    throw new PHPBackendException("invalid value in setOffice param method");
		}
	}

	/**
	 * @param VirtualMoney  $response
	 */
	public function setResponse($response) : void {
		if ($response == null || $response instanceof VirtualMoney) {
			$this->response = $response;
		}else if (self::isInt($response)) {
			$this->response = new VirtualMoney(array('id' => $response));
		} else {
		    throw new PHPBackendException("Invalid param value in setResponse Method");
		}
	}

	/**
	 * Get montant prevue pour l'achat de produit
	 * @return  float
	 */ 
	public function getProduct() : ?float
	{
		return $this->product;
	}

	/**
	 * Set montant prevue pour l'achat de produit
	 * @param  float  $product
	 */ 
	public function setProduct(float $product) : void
	{
		$this->product = $product;
	}

	/**
	 * Get montant prevue pour l'afilisation
	 * @return  float
	 */ 
	public function getAffiliation() : ?float
	{
		return $this->affiliation;
	}

	/**
	 * Set montant prevue pour l'afilisation
	 * @param  float  $affiliation
	 */ 
	public function setAffiliation(float $affiliation) : void
	{
		$this->affiliation = $affiliation;
	}

	/**
	 * Get the value of withdrawalsCount
	 */ 
	public function getWithdrawalsCount() : int
	{
		return $this->withdrawalsCount;
	}

	/**
	 * Set the value of withdrawalsCount
	 * @return  self
	 */ 
	public function setWithdrawalsCount(int $withdrawalsCount) : void
	{
		$this->withdrawalsCount = $withdrawalsCount;
	}
}

