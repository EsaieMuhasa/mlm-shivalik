<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\OfficeBonus;
use Core\Shivalik\Managers\OfficeBonusDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class OfficeBonusDAOManagerImplementation1 extends OfficeBonusDAOManager
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
     * @see \Core\Shivalik\Managers\AbstractOperationDAOManager::update()
     */
    public function update($entity, $id) : void
    {
        throw new DAOException("no subsequent update of the office bonus is authorized");
    }

}

