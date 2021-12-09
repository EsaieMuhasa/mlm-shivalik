<?php
namespace Entities;

use Library\IllegalFormValueException;
use Library\LibException;

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
     * @var RaportWithdrawal
     */
    private $raport;
    
    /**
     * @return \Entities\Office
     */
    public function getOffice() : ?Office
    {
        return $this->office;
    }

    /**
     * @return \Entities\OfficeAdmin
     */
    public function getAdmin() : ?OfficeAdmin
    {
        return $this->admin;
    }

    /**
     * @param \Entities\Office $office
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
     * @param \Entities\OfficeAdmin $admin
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
     * @return \Entities\RaportWithdrawal
     */
    public function getRaport() : ?RaportWithdrawal
    {
        return $this->raport;
    }

    /**
     * @param \Entities\RaportWithdrawal $raport
     */
    public function setRaport($raport) : void
    {
        if ($raport == null || $raport instanceof RaportWithdrawal) {
            $this->raport = $raport;
        }else if(self::isInt($raport)){
            $this->raport = new RaportWithdrawal(array('id' => $raport));
        } else {
            throw new LibException("invalid value as a parameter of the setRaport() method");
        }
    }


}

