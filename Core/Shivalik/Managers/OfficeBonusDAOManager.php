<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\OfficeBonus;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

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
    public function checkByVirtual (int $virtualId) : bool {
        return $this->columnValueExist("virtualMoney", $virtualId);
    }
    
    /**
     * renvoie le bonus liee a un montant virtuel envoyeee a un office
     * @param int $virtualId
     * @return OfficeBonus
     */
    public function findByVirtual (int $virtualId) : OfficeBonus {
        return UtilitaireSQL::findUnique($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "virtualMoney", $virtualId);
    }
}

