<?php
namespace Entities;

use Library\LibException;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeAdmin extends User
{
    /**
     * @var Office
     */
    private $office;
    
    /**
     * @return \Entities\Office
     */
    public function getOffice() : ?Office
    {
        return $this->office;
    }

    /**
     * @param \Entities\Office $office
     */
    public function setOffice($office) : void
    {
        if ($this->isInt($office)) {
            $this->office = new Office(array('id' => $office));
        }else if ($office instanceof Office || $office == null) {
            $this->office = $office;
        }else {
            throw new LibException("Invalid value in param of setOffice() method");
        }
    }

}

