<?php
namespace Managers;

use Library\DAOException;
use Entities\OfficeBonus;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class OfficeBonusDAOManager extends AbstractOperationDAOManager
{
    /**
     * est-ce que ce virtual a un bonnus???
     * @param int $virtualId
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     */
    public function virtualHasBonus (int $virtualId) : bool {
        return $this->columnValueExist("virtualMoney", $virtualId);
    }
    
    /**
     * revoie le bonus liee a un montant virtuel envoyeee a un office
     * @param int $virtualId
     * @return OfficeBonus
     */
    public function getVirtualBonus (int $virtualId) : OfficeBonus {
        return $this->pdo_uniqueFromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), "virtualMoney", $virtualId);
    }
}

