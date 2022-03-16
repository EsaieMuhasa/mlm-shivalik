<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\OfficeBonus;
use Core\Shivalik\Managers\OfficeBonusDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class OfficeBonusDAOManagerImplementation1 extends AbstractOperationDAOManager implements  OfficeBonusDAOManager
{
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param OfficeBonus $bonus
     */
    public function createInTransaction($bonus, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'generator' => $bonus->getGenerator()->getId(),
            'member' => $bonus->getMember()->getId(),
            'virtualMoney' => $bonus->getVirtualMoney()->getId(),
            'amount' => $bonus->getAmount(),
            self::FIELD_DATE_AJOUT => $bonus->getFormatedDateAjout()
        ]);
        $bonus->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\AbstractOperationDAOManager::update()
     */
    public function update($entity, $id) : void
    {
        throw new DAOException("no subsequent update of the office bonus is authorized");
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeBonusDAOManager::checkByVirtual()
     */
    public function checkByVirtual (int $virtualId) : bool {
        return $this->columnValueExist("virtualMoney", $virtualId);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeBonusDAOManager::findByVirtual()
     */
    public function findByVirtual (int $virtualId) : OfficeBonus {
        return UtilitaireSQL::findUnique($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "virtualMoney", $virtualId);
    }

}

