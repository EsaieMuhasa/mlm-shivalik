<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MHS
 *        
 */
class GradeMember extends DBEntity
{
    /**
     * @var \DateTime
     */
    private $initDate;
    
    /**
     * @var \DateTime
     */
    private $closeDate;
    
    /**
     * @var double
     */
    private $membership;
    
    /**
     * @var double
     */
    private $product;
    
    /**
     * @var float
     */
    private $officePart;
    
    /**
     * @var GradeMember
     */
    private $old;
    
    /**
     * @var Member
     */
    private $member;

    /**
     * @var Grade
     */
    private $grade;
    
    /**
     * @var Office
     */
    private $office;
    
    /**
     * @var boolean
     */
    private $enable;
    
    /**
     * Forfait virtuel pour lequel l'operation fait reference pour une retrocession
     * @var VirtualMoney
     * @deprecated pour des raisons d'amelioration de la gestion de virtuel ce champs ne doit plus etre utiliser
     */
    private $virtualMoney;
    
    /**
     * @var MonthlyOrder
     */
    private $monthlyOrder;
    
    /**
	 * @return Office
	 */
	public function getOffice() :?Office {
		return $this->office;
	}

	/**
	 * @param Office $office
	 */
	public function setOffice($office) : void  {
		if ($office == null || $office instanceof Office) {
			$this->office = $office;
		} else if (self::isInt($office)) {
			$this->office = new Office(array('id' => $office));
		} else {
		    throw new PHPBackendException("Illegal value in setOffice() method param");
		}
	}

	/**
     * @return \DateTime
     */
    public function getInitDate() : ?\DateTime
    {
        return $this->initDate;
    }

    /**
     * @return \DateTime
     */
    public function getCloseDate() :?\DateTime
    {
        return $this->closeDate;
    }

    /**
     * @return float
     */
    public function getMembership() : ?float
    {
        return $this->membership;
    }

    /**
     * @return float
     */
    public function getProduct() : ?float
    {
        return $this->product;
    }

    /**
     * @return GradeMember
     */
    public function getOld() : ?GradeMember
    {
        return $this->old;
    }

    /**
     * @return Member
     */
    public function getMember() : ?Member
    {
        return $this->member;
    }

    /**
     * @return Grade
     */
    public function getGrade() : ?Grade
    {
        return $this->grade;
    }

    /**
     * @param \DateTime $initDate
     */
    public function setInitDate($initDate) : void 
    {
        $this->initDate = $this->hydrateDate($initDate);
    }

    /**
     * @param \DateTime $closeDate
     */
    public function setCloseDate($closeDate) : void
    {
        $this->closeDate = $this->hydrateDate($closeDate);
    }

    /**
     * @param number $membership
     */
    public function setMembership($membership) : void
    {
        $this->membership = @floatval($membership);
    }

    /**
     * @param number $product
     */
    public function setProduct($product) : void
    {
        $this->product = @floatval($product);
    }

    /**
     * @param GradeMember $old
     */
    public function setOld($old) : void
    {
        if ($old == null || $old instanceof GradeMember) {
            $this->old = $old;
        }elseif ($this->isInt($old)){
            $this->old = new GradeMember(array('id' => $old));
        }else {
            throw new PHPBackendException("Invalid value in param of method setOld()");
        }
    }

    /**
     * @param Member $member
     */
    public function setMember($member) : void
    {
        if ($member == null || $member instanceof Member) {
            $this->member = $member;
        }elseif ($this->isInt($member)){
            $this->member = new Member(array('id' => $member));
        }else {
            throw new PHPBackendException("Invalid value in param of method setMember()");
        }
    }

    /**
     * @param Grade $grade
     */
    public function setGrade($grade) : void
    {
        if ($grade == null || $grade instanceof Grade) {
            $this->grade = $grade;
        }elseif ($this->isInt($grade)){
            $this->grade = new Grade(array('id' => $grade));
        }else {
            throw new PHPBackendException("Invalid value in param of method setGrade()");
        }
    }
    
    
    
    /**
     * @return boolean
     */
    public function isEnable() :?bool
    {
        return $this->enable;
    }

    /**
     * @param boolean $enable
     */
    public function setEnable($enable) : void
    {
        $this->enable = ($enable==true || $enable >=1 || $enable == 'true');
    }
    
    /**
     * @return float
     */
    public function getOfficePart()
    {
        return $this->officePart;
    }

    /**
     * @param number $officePart
     */
    public function setOfficePart($officePart)
    {
        $this->officePart = @floatval($officePart);
    }
    
    /**
     * @return VirtualMoney
     * @deprecated pour des raisons liee au changement de la logique de gestion des virtuel cette methode n'est plus d'actualite
     */
    public function getVirtualMoney() : ?VirtualMoney
    {
        return $this->virtualMoney;
    }

    /**
     * @param VirtualMoney $virtualMoney
     * @deprecated pour pour des raisons de changement de la logique de gestion des virtule,
     * cette methode ne dois plus etre utiliser
     */
    public function setVirtualMoney($virtualMoney) : void {
        if ($virtualMoney instanceof VirtualMoney || $virtualMoney == null) {
            $this->virtualMoney = $virtualMoney;
        }elseif (self::isInt($virtualMoney)) {
            $this->virtualMoney = new VirtualMoney(array('id' => $virtualMoney));
        }else {
            throw new PHPBackendException("invalide param value type in setVirtualMoney() param method");
        }
    }
    /**
     * @return \Core\Shivalik\Entities\MonthlyOrder
     */
    public function getMonthlyOrder () : ?MonthlyOrder{
        return $this->monthlyOrder;
    }

    /**
     * @param \Core\Shivalik\Entities\MonthlyOrder | int $monthlyOrder
     */
    public function setMonthlyOrder ($monthlyOrder) : void {
        if ($monthlyOrder == null || $monthlyOrder instanceof MonthlyOrder) {
            $this->monthlyOrder = $monthlyOrder;
        } else if (self::isInt($monthlyOrder)) {
            $this->monthlyOrder = new MonthlyOrder(['id' => $monthlyOrder]);
        } else {
            throw new PHPBackendException("invalide param value type in setMonthlyOrder() param method");
        }
    }

}

