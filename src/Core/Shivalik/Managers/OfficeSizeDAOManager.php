<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\OfficeSize;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
interface OfficeSizeDAOManager extends DAOInterface
{
    /**
     * @param OfficeSize $os
     * @throws DAOException
     */
    public function upgrade (OfficeSize $os) : void;
    
    /**
     * renvoie la taile actuel de l'office
     * @param int $officeId
     * @return OfficeSize
     */
    public function findCurrentByOffice (int $officeId) : OfficeSize;
    
    /**
     * L'office as-t-elle aumoin un size???
     * @param int $officeId
     * @return bool
     */
    public function checkByOffice (int $officeId) : bool;
    
    /**
     * revoie la collction de pack d'un office
     * @param int $officeId
     * @return array
     */
    public function findByOffice (int $officeId) : array ;
}

