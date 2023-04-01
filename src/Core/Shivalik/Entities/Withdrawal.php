<?php
namespace Core\Shivalik\Entities;

use PHPBackend\PHPBackendException;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MHS
 *        
 */
class Withdrawal extends Operation
{
    /**
     * @var Office
     */
    private $office;
    
    /**
     * @var OfficeAdmin
     */
    private $admin;
    
    /**
     * @var string
     */
    private $telephone;
    
    /**
     * @var RequestVirtualMoney
     */
    private $raport;

    /** @var string */
    private $memberName;

    /** @var string */
    private $memberPostName;

    /** @var string */
    private $memberLastName;
    
    /** @var string */
    private $memberPhoto;
    
    /** @var string */
    private $memberMatricule;
    
    /**
     * @return Office
     */
    public function getOffice() : ?Office
    {
        return $this->office;
    }

    /**
     * @return OfficeAdmin
     */
    public function getAdmin() : ?OfficeAdmin
    {
        return $this->admin;
    }

    /**
     * @param Office $office
     */
    public function setOffice($office) : void
    {
        if ($office == null || $office instanceof Office) {
            $this->office = $office;
        }elseif ($this->isInt($office)) {
            $this->office = new Office(array('id' => $office));
        }else{
            throw new IllegalFormValueException("invalid value by method setOffice()");
        }
    }

    /**
     * @param OfficeAdmin $admin
     */
    public function setAdmin($admin) : void 
    {
        if ($admin == null || $admin instanceof OfficeAdmin) {
            $this->admin = $admin;
        }elseif ($this->isInt($admin)) {
            $this->admin = new OfficeAdmin(array('id' => $admin));
        }else{
            throw new IllegalFormValueException("invalid value by method setAdmin()");
        }
    }
    
	/**
	 * @return string
	 */
	public function getTelephone() :?string {
		return $this->telephone;
	}

	/**
	 * @param string $telephone
	 */
	public function setTelephone($telephone) : void {
		$this->telephone = $telephone;
	}
    /**
     * @return RequestVirtualMoney
     */
    public function getRaport() : ?RequestVirtualMoney
    {
        return $this->raport;
    }

    /**
     * @param RequestVirtualMoney $raport
     */
    public function setRaport($raport) : void
    {
        if ($raport == null || $raport instanceof RequestVirtualMoney) {
            $this->raport = $raport;
        }else if(self::isInt($raport)){
            $this->raport = new RequestVirtualMoney(array('id' => $raport));
        } else {
            throw new PHPBackendException("invalid value as a parameter of the setRaport() method");
        }
    }



    /**
     * Get the value of memberName
     */ 
    public function getMemberName()
    {
        return $this->memberName;
    }

    /**
     * Set the value of memberName
     *
     * @return  self
     */ 
    public function setMemberName($memberName)
    {
        $this->memberName = $memberName;

        return $this;
    }

    /**
     * Get the value of memberPostName
     */ 
    public function getMemberPostName()
    {
        return $this->memberPostName;
    }

    /**
     * Set the value of memberPostName
     *
     * @return  self
     */ 
    public function setMemberPostName($memberPostName)
    {
        $this->memberPostName = $memberPostName;

        return $this;
    }

    /**
     * Get the value of memberLastName
     */ 
    public function getMemberLastName()
    {
        return $this->memberLastName;
    }

    /**
     * Set the value of memberLastName
     *
     * @return  self
     */ 
    public function setMemberLastName($memberLastName)
    {
        $this->memberLastName = $memberLastName;

        return $this;
    }

    /**
     * Get the value of memberPhoto
     */ 
    public function getMemberPhoto()
    {
        return $this->memberPhoto;
    }

    /**
     * Set the value of memberPhoto
     *
     * @return  self
     */ 
    public function setMemberPhoto($memberPhoto)
    {
        $this->memberPhoto = $memberPhoto;

        return $this;
    }

    /**
     * Get the value of memberMatricule
     */ 
    public function getMemberMatricule()
    {
        return $this->memberMatricule;
    }

    /**
     * Set the value of memberMatricule
     *
     * @return  self
     */ 
    public function setMemberMatricule($memberMatricule)
    {
        $this->memberMatricule = $memberMatricule;

        return $this;
    }

    public function getMemberNames () {
        return "{$this->memberName} {$this->memberPostName} {$this->memberLastName}";
    }
}

