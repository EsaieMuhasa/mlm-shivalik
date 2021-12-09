<?php
namespace Managers;

use Library\AbstractDAOManager;
use Entities\OfficeSize;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class OfficeSizeDAOManager extends AbstractDAOManager
{
    /**
     * @param OfficeSize $os
     * @throws DAOException
     */
    public abstract function upgrade (OfficeSize $os) : void;
    
    /**
     * @param int $officeId
     * @return OfficeSize
     */
    public abstract function getCurrent ($officeId) : OfficeSize;
    
    /**
     * L'office as-t-elle aumoin un size???
     * @param int $officeId
     * @return bool
     */
    public function hasSize (int $officeId) : bool{
        return $this->columnValueExist("office", $officeId);
    }
}

