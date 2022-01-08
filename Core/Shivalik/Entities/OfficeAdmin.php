<?php
namespace Core\Shivalik\Entities;

use PHPBackend\PHPBackendException;


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
     * @return Office
     */
    public function getOffice() : ?Office
    {
        return $this->office;
    }

    /**
     * @param Office $office
     */
    public function setOffice($office) : void
    {
        if ($this->isInt($office)) {
            $this->office = new Office(array('id' => $office));
        }else if ($office instanceof Office || $office == null) {
            $this->office = $office;
        }else {
            throw new PHPBackendException("Invalid value in param of setOffice() method");
        }
    }

}

