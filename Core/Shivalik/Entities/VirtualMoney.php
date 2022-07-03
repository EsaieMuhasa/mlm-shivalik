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
	 * @deprecated les histoires des dettes ne doivent plus etre prise en charge.
	 * c'est ainsi que le montant prevue pour les afiliations, et celuis d'achat des produits, doivent etre separer
	 */
	private $amount;
	
	/**
	 * Corespond au produit expedier
	 * @var number
	 */
	private $product = 0;
	
	/**
	 * le nontant utilisatble pour le compte produit
	 * @var number
	 */
	private $availableProduct = 0;
	
	/**
	 * Montant utilisable pour le compte affiliation
	 * @var number
	 */
	private $availableAfiliate = 0;
	
	/**
	 * le montant deja utiliserpour le compte produit
	 * @var integer
	 */
	private $usedProduct = 0;
	
	/**
	 * Montant deja utiliser pour le compte afiliation
	 * @var integer
	 */
	private $usedAfiliate = 0;
	
	/**
	 * Correspond au montant prevue pour l'afiliation
	 * @var number
	 */
	private $afiliate = 0;
	
	/**
	 * montant virtuel accorder
	 * @var number
	 * @deprecated
	 */
	private $expected;
	
	/**
	 * collection d'operations.
	 * pour chaque operation, une retrocession doit etre soustrait au montant attendue
	 * @var GradeMember[]
	 * @deprecated la depreciation de cette attribut viens du faite que les dette ne doivent plus etre admisent
	 */
	private $debts = [];
	
	/**
	 * @var Office
	 */
	private $office;
	
	/**
	 * @var RequestVirtualMoney
	 * @deprecated
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
	 * @deprecated pour de raison de changement logique,
	 * le champs amount ne dois plus etre utiliser.
	 */
	public function getAmount() : int {
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
	 * @deprecated
	 */
	protected function setAmount($amount) : void {
		$this->amount = @intval($amount, 10);
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
     * @deprecated pour de raison de changement de logique de gestion de virtuel, cette medode n'est plus d'actualite
     * et dans bon nombre des cas, il renvera NULL
     */
    public function getRequest() : ?RequestVirtualMoney
    {
        return $this->request;
    }

    /**
     * @param RequestVirtualMoney $request
     * @deprecated pour des raison de changement de logique de gestion des virtuel, cette medode n'auras plus d'effet sur 
     * l'etat d'un instance de cette classe
     */
    public function setRequest($request): void {
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
     * @deprecated
     */
    public function getExpected()
    {
        return $this->expected;
    }

    /**
     * @param number $expected
     * @deprecated
     */
    public function setExpected($expected) : void
    {
        $this->expected = @intval($expected, 10);
    }
    
    /**
     * @return GradeMember[]
     * @deprecated depuis que les dettes ne doivent plus etre admisent, cette methode est suceptible revoyer toujours un table vide
     */
    public function getDebts()
    {
        return $this->debts;
    }

    /**
     * @param GradeMember[]  $debts
     * @deprecated les dettes ne sont plus admisent
     */
    public function setDebts($debts) : void 
    {
        $this->debts = $debts;
    }
    
    /**
     * @param GradeMember $gm
     * @deprecated les dettes ne sont plus admisent
     */
    public function addDebt (GradeMember $gm) : void {
        $this->debts[] = $gm;
    }
    
    /**
     * Revoie le solde des operations qui ont toucher le montant attendue
     * @return int
     * @deprecated les dettes ne sont plus admisent
     */
    public function getSoldDebts () : int {
        $sold = 0;
        foreach ($this->getDebts() as $debt) {//la dette touche uniquement l'adhession
            $sold += $debt->getMembership();
        }
        return $sold;
    }
    
    /**
     * @return number
     */
    public function getProduct () : int{
        return $this->product;
    }

    /**
     * @return number
     */
    public function getAfiliate () : int{
        return $this->afiliate;
    }

    /**
     * @param number $product
     */
    public function setProduct ($product) : void{
        $this->product = $product;
    }

    /**
     * @param number $afiliate
     */
    public function setAfiliate ($afiliate) : void {
        $this->afiliate = @intval($afiliate, 10);
    }
    
    /**
     * ajout d'un montant au compte d'affiliation
     * @param float $amount
     */
    public function addOnAfiliate (float $amount) : void {
        $this->afiliate += $amount;
        $this->setAvailableAfiliate($this->getAvailableAfiliate() +  $amount);
    }
    
    /**
     * Ajout d'un montant au compte produit
     * @param float $amount
     */
    public function addOnProduct (float $amount) : void {
        $this->product += $amount;
        $this->setAvailableProduct($this->getAvailableProduct() + $amount);
    }
    
    /**
     * renvoie le nontant utilisable pour le compte produit
     * @return number
     */
    public function getAvailableProduct() : int{
        if ($this->availableProduct === null) {
            $this->availableProduct = $this->product;
        }
        return $this->availableProduct;
    }

    /**
     * renvoie le montant utilisable pour le compte affiliation
     * @return number
     */
    public function getAvailableAfiliate() : int {
        if($this->availableAfiliate === null) {
            $this->availableAfiliate = $this->afiliate;
        }
        return $this->availableAfiliate;
    }
    
    /**
     * renvoie le montant deja utiliser pour le compte produit
     * @return number
     */
    public function getUsedProduct() : int{
        if ($this->usedProduct === null) {
            return 0;
        }
        return $this->usedProduct;
    }

    /**
     * renvoie le montant deja utiliser pour le compte afiliation
     * @return number
     */
    public function getUsedAfiliate() : int{
        if ($this->usedAfiliate === null) {
            return 0;
        }
        return $this->usedAfiliate;
    }

    /**
     * renvoie le montant retranchable au montant produit, pour le montant proposer en parametre
     * @param float $amount
     * @return float
     */
    public function getSubstractableToAvailableProduct (float $amount) : float {
        if($this->getAvailableProduct() != 0){ 
            if($this->getAvailableProduct() >= $amount) {
                return $amount;
            }
            return $this->getAvailableProduct();
        }
        return 0;
    }
    
    /**
     * sutraction d'une operation fraichement faite
     * @param int $product
     * @param int $afiliate
     */
    public function substract (int $product, int $afiliate) : void {
//         var_dump($this);
        $this->setAvailableAfiliate($this->getAvailableAfiliate() - $afiliate);
        $this->setAvailableProduct($this->getAvailableProduct() - $product);
        
        $this->setUsedProduct($this->getUsedProduct() + $product);
        $this->setUsedAfiliate($this->getUsedAfiliate() + $afiliate);
//         var_dump($this);
//         exit();
    }
    
    /**
     * renvoie le montant substractible au compte d'affiliation pour le montant proposer en parametre
     * @param float $amount
     * @return float
     */
    public function getSubstractableToAvailableAfiliate (float $amount) : float {
        if($this->getAvailableAfiliate() != 0){
            if($this->getAvailableAfiliate() >= $amount) {
                return $amount;
            }
            return $this->getAvailableAfiliate();
        }
        return 0;
    }
    
    /**
     * l'appele a cette methode ce fait de maniere introspective par la methode hydrate de la 
     * classe de parente
     * @param number $availableProduct
     */
    protected function setAvailableProduct($availableProduct) : void {
        $this->availableProduct = is_null($availableProduct)? $availableProduct : @intval($availableProduct, 10) ;
    }

    /**
     * l'appele a cette methode ce fait de maniere infrospective par la methode hydrate de la classe parante
     * @param number $availableAfiliate
     */
    protected function setAvailableAfiliate($availableAfiliate) : void {
        $this->availableAfiliate = (!is_null($availableAfiliate)? @intval($availableAfiliate, 10) : $availableAfiliate);
    }
    
    /**
     * l'appel a cette methode ce fait via la methode hydrate lors du chargement des donnees depuis la base de donnee
     * @param number $usedProduct
     */
    protected function setUsedProduct($usedProduct) : void {
        $this->usedProduct = $usedProduct !== null? @intval($usedProduct, 10) : $usedProduct;
    }

    /**
     * l'appel a cette methode se fait via la methode hydrate lors du chargment des donnees
     * il est meme recommander de n'est pas manipuler le donnees de ce champs en dur
     * @param number $usedAfiliate
     */
    protected function setUsedAfiliate($usedAfiliate) : void {
        $this->usedAfiliate = $usedAfiliate !== null? @intval($usedAfiliate, 10): $usedAfiliate;
    }



}

