<?php
namespace Managers\Implementation;

use Managers\OfficeBonusDAOManager;
use Library\DAOException;
use Entities\OfficeBonus;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class OfficeBonusDAOManagerImplementation1 extends OfficeBonusDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     * @param OfficeBonus $entity
     */
    public function create($entity)
    {
        try {
            if ($this->pdo->beginTransaction()) {
                $this->createInTransaction($entity, $this->pdo);
                $this->pdo->commit();
            }else {
                throw new DAOException("An error occurred while creating the transaction");
            }
        } catch (\PDOException $e) {
            throw new DAOException("An error occurred in the plain banefice sharing transaction: {$e->getMessage()}", intval($e->getCode()), $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::createInTransaction()
     * @param OfficeBonus $bonus
     */
    public function createInTransaction($bonus, $api): void
    {
        $id = $this->pdo_insertInTableTansactionnel($api, $this->getTableName(), array(
            'generator' => $bonus->getGenerator()->getId(),
            'member' => $bonus->getMember()->getId(),
            'virtualMoney' => $bonus->getVirtualMoney()->getId(),
            'amount' => $bonus->getAmount()
        ));
        $bonus->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        throw new DAOException("no subsequent update of the office bonus is authorized");
    }

}

