<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\OfficeSize;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class OfficeSizeDAOManager extends DefaultDAOInterface
{
    /**
     * @param OfficeSize $os
     * @throws DAOException
     */
    public abstract function upgrade (OfficeSize $os) : void;
    
    /**
     * renvoie la taile actuel de l'office
     * @param int $officeId
     * @return OfficeSize
     */
    public abstract function findCurrentByOffice (int $officeId) : OfficeSize;
    
    /**
     * L'office as-t-elle aumoin un size???
     * @param int $officeId
     * @return bool
     */
    public function checkByOffice (int $officeId) : bool{
        return $this->columnValueExist("office", $officeId);
    }
    
    /**
     * revoie la collction de pack d'un office
     * @param int $officeId
     * @return array
     */
    public function findByOffice (int $officeId) : array {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, array('office' => $officeId));
    }
}

