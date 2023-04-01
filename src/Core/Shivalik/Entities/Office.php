<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MHS
 *        
 */
class Office extends DBEntity
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $photo;
    
    /**
     * visibilite du bureau
     * @var boolean
     */
    private $visible;
    
    /**
     * @var boolean
     */
    private $central;
    
    /**
     * @var Localisation
     */
    private $localisation;
    
    /**
     * @var Member
     */
    private $member;
    
    
    /**
     * collection des opperations qui touche la monais virtuel
     * @var GradeMember[]
     */
    private $operations = [];
    
    /**
     * collection des stock virtuel de l'office
     * @var VirtualMoney[]
     */
    private $virtualMoneys = [];
    
    /**
     * @var RequestVirtualMoney[]
     */
    private $requests = [];
    
    
    /**
     * collection des straits effectuer dans un bureau
     * @var Withdrawal[]
     */
    private $withdrawals = [];
    
    
    /**
     * L'actuel size de l'office
     * @var OfficeSize
     */
    private $officeSize;

	//montant mise en case ou recuperer depuis une vue materiel dans la BDD
	/** @var float */ // sommes de demande de matching dans l'office
	private $amountWithdrawals;
    
    
    /**
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isCentral() : ?bool
    {
        return $this->central;
    }

    /**
     * @param boolean $central
     */
    public function setCentral($central) : void
    {
        $this->central = $central==true || $central >= 1;
    }

    /**
     * @return string
     */
    public function getPhoto() : ?string
    {
        return $this->photo;
    }

    /**
     * @return Localisation
     */
    public function getLocalisation() : ?Localisation
    {
        return $this->localisation;
    }

    /**
     * @param string $name
     */
    public function setName($name) : void
    {
        $this->name = $name;
    }

    /**
     * @param string $photo
     */
    public function setPhoto($photo) : void
    {
        $this->photo = $photo;
    }

    /**
     * @param Localisation $localisation
     */
    public function setLocalisation($localisation) : void
    {
        if ($this->isInt($localisation)) {
            $this->localisation = new Localisation(array('id' => $localisation));
        }elseif ($localisation  instanceof Localisation || $localisation == null){
            $this->localisation = $localisation;
        }else {
            throw new PHPBackendException("invalid value in setLocation method maparam");
        }
            
    }
    
	/**
	 * @return Member
	 */
	public function getMember() :?Member {
		return $this->member;
	}

	/**
	 * @param Member $member
	 */
	public function setMember($member) : void {
		if ($member == null || $member instanceof Member) {
			$this->member = $member;
		}elseif ($this->isInt($member)){
			$this->member = new Member(array('id' => $member));
		}else {
			throw new PHPBackendException("invalid value in method parameter setMember()");
		}
	}
	
	/**
	 * @return multitype:Withdrawal 
	 */
	public function getWithdrawals() {
		return $this->withdrawals;
	}

	/**
	 * @param multitype:Withdrawal  $withdrawals
	 */
	public function setWithdrawals(array $withdrawals) : void{
		$this->withdrawals = $withdrawals;
	}

	/**
	 * @return GradeMember[]
	 */
	public function getOperations() {
		return $this->operations;
	}
	
	/**
	 * Revoie le solde de operation deja fait par l'office
	 * @return int
	 */
	public function getSoldOperations () : int {
		$solde = 0;
		foreach ($this->operations as $operation) {
			$solde += ($operation->getMembership() + $operation->getProduct());
		}
		return $solde;
	}
	
	/**
	 * renvoie le solde des produits
	 * lors montant allouer pour l'adhesion a un packet
	 * @return int
	 * @deprecated
	 */
	public function getSoldProduct() : int {
	    $solde = 0;
	    foreach ($this->operations as $operation) {
	        $solde += $operation->getMonthlyOrder() != null? 0 : $operation->getProduct();
	    }
	    return $solde;
	}
	
	/**
	 * Revoie le solde de retro-commission, pour tout les operations deja fait
	 * en ignorant les retrocommissions deja payer
	 * @return int
	 * @deprecated deprecier lors de la ceparation des comptes des virtuels
	 */
	public function getSoldRetroCommission() : int {
	    $solde = 0;
	    foreach ($this->operations as $operation) {
	        if ($operation->getVirtualMoney() == null) {
    	        $solde += $operation->getMembership();
	        }
	    }
	    return $solde;
	}
	
	/**
	 * Revoei la somme des dettes de retro-commisssion pour l'ensemble des operations
	 * qu'a deja effecctuer un office, sans que ceux-ci ne soient deja payer
	 * @return int
	 * @deprecated le dettes ne sont plus pris en charge depuis que le compte des virtuels des produits et des affiliations ont ete separer
	 */
	public function getSoldDebt () : int {
	    $debt = 0;
	    foreach ($this->getOperations() as $operation) {
	        if ($operation->getVirtualMoney() == null) {//pour les operations dont la retrocomission est deja au rendez-vous
    	        $debt += $operation->getMembership();
	        }
	        
	    }
	    return $debt;
	}
	
	/**
	 * renvoie le solde du monai virtual deja utiliser
	 * @return int
	 * @deprecated le le montanta d'achat des poduits et le nontant d'affiliation sont maintenant separer
	 */
	public function getSoldTrashVirtualMoney () : int {
	    return ($this->getSoldProduct() + $this->getSoldRetroCommission());
	}
	
	/**
	 * @return number
	 */
	public function getSoldWithdrawals () {
		$solde = 0;
		foreach ($this->withdrawals as $withdrawal) {
			$solde += $withdrawal->getAmount();
		}
		return $solde;
	}
	
	/**
	 * @return number
	 */
	public function getSoldRequestWithdrawals () {
		$solde = 0;
		foreach ($this->withdrawals as $withdrawal) {
			if ($withdrawal->getAdmin() == null) {
				$solde += $withdrawal->getAmount();
			}
		}
		return $solde;
	}
	
	/**
	 * @return number
	 */
	public function getSoldAcceptWithdrawals () {
		$solde = 0;
		foreach ($this->withdrawals as $withdrawal) {
			if ($withdrawal->getAdmin() != null && $withdrawal->getRaport() == null) {
				$solde += $withdrawal->getAmount();
			}
		}
		return $solde;
	}
	

	/**
	 * @return VirtualMoney[]
	 */
	public function getVirtualMoneys() {
		return $this->virtualMoneys;
	}
	
	/**
	 * revoie le solde disponible de la monais virtuel d'un office
	 * @return int
	 * @deprecated la depreciation de cette methode viens du fait que la logique de gestion des virtuels a changer
	 * les dettes ne sont plus prisent en charge
	 */
	public function getAvailableVirtualMoney () : int {
		$money = 0;
		foreach ($this->virtualMoneys as $virtual) {
			$money += $virtual->getAmount();
		}
		
		$return = ($money- $this->getUsedVirtualMoney());
		return $return < 0? 0 : $return;
	}
	
	/**
	 * renvoie le solde diponible du virtuel d'affiliation, produit a acheter
	 * @return float
	 */
	public function getAvailableVirtualMoneyProduct () : int {
	    $money = 0;
	    foreach ($this->virtualMoneys as $virtual) {
	        $money += $virtual->getAvailableProduct();
	    }
	    return $money;
	}
	
	/**
	 * renvoie le montant utilisable pour le compte des adhesion
	 * @return int
	 */
	public function getAvailableVirualMoneyAfiliate () : int {
	    $money = 0;
	    foreach ($this->virtualMoneys as $virtual) {
	        $money += $virtual->getAvailableAfiliate();
	    }
	    return $money;
	}
	
	/**
	 * Renvoie la somme des montants d'achat des produits lors des affiliation
	 * ou de mis en niveau des comptes des membres
	 * @return int
	 */
	public function getTrashVirtualMoneyProduct () : int  {
	    $money = 0;
	    foreach ($this->virtualMoneys as $virtual) {
	        $money += $virtual->getUsedProduct();
	    }
	    return $money;
	}
	
	/**
	 * Renvoie le sold des montants des adhesions lors des affiliations
	 * @return int
	 */
	public function getTrashVirtualMoneyAfiliate () : int  {
	    $money = 0;
	    foreach ($this->virtualMoneys as $virtual) {
	        $money += $virtual->getUsedAfiliate();
	    }
	    return $money;
	}
	
	/**
	 * Aliance de la methode getSoldOperation()
	 * @return int
	 * @deprecated la depreciation de cette methode viens du faite que la logique de gestion des virtuels a changer
	 * Le dettes ne sont plus prisent en charge 
	 */
	public function getUsedVirtualMoney () : int {
		return $this->getSoldTrashVirtualMoney();
	}
	
	/**
	 * l'office est en dete??
	 * @return bool
	 * @deprecated les dettes ne sont plus pris en charge
	 */
	public function hasDebts () : bool {
		if ($this->getDebts() != 0 ) {
			return true;
		}
		return false;
	}
	
	/**
	 * renvoie la dette de l'office
	 * @return int
	 * @deprecated les dettes ne sont plus prise en charge
	 */
	public function getDebts () : int {
		$money = 0;
		foreach ($this->virtualMoneys as $virtual) {
			$money += $virtual->getAmount();
		}
		$return = ($money - $this->getSoldOperations());
		
		return $return < 0? abs($return) : 0;
	}

	/**
	 * @return multitype:RequestVirtualMoney 
	 * @deprecated les requettes de demande de virtuel ne sont plus pris en charge
	 */
	public function getRequests() {
		return $this->requests;
	}

	/**
	 * @param multitype:RequestVirtualMoney  $requests
	 * @deprecated les requettes de demande de virtuel ne sont plus prisent en charge
	 */
	public function setRequests(array $requests) {
		$this->requests = $requests;
	}

	/**
	 * @param GradeMember[]  $operations
	 */
	public function setOperations($operations) {
		$this->operations = $operations;
	}
	
	/**
	 * comptage des upgrade des comptes
	 * @return int
	 */
	public function countUpgrades () : int {
		$nombre = 0;
		foreach ($this->operations as $op) {
			if ($op->getOld() != null) {
				$nombre++;
			}
		}
		return $nombre;
	}
	
	/**
	 * @param VirtualMoney[]  $virtualMoneys
	 */
	public function setVirtualMoneys($virtualMoneys) {
		$this->virtualMoneys = $virtualMoneys;
	}
	
    /**
     * @return OfficeSize
     */
    public function getOfficeSize() : ?OfficeSize
    {
        return $this->officeSize;
    }

    /**
     * @param OfficeSize $officeSize
     */
    public function setOfficeSize($officeSize)
    {
        if ($officeSize instanceof OfficeSize || $officeSize == null) {
            $this->officeSize = $officeSize;
        } elseif (self::isInt($officeSize)) {
            $this->officeSize = new OfficeSize(array('id' => $officeSize));
        } else {
            throw new PHPBackendException("invalid param value in setOfficeSize() method");
        }
    }
    /**
     * @return boolean
     */
    public function isVisible() : ?bool
    {
        return $this->visible;
    }

    /**
     * @param boolean $visible
     * modification de la visiblite d'un office
     */
    public function setVisible ($visible) : void
    {
        $this->visible = is_bool($visible)? $visible : $visible == 1;
    }

}

