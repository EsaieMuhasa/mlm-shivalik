<?php
namespace Managers\Implementation;

use Managers\TransferDAOManager;
use Library\DAOException;
use Entities\Transfer;

/**
 *
 * @author Esaie MHS
 *        
 */
class TransferDAOManagerImplementation1 extends TransferDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     * @param Transfer $entity
     */
    public function create($entity)
    {
        try {
            $id = $this->pdo_insertInTable($this->getTableName(), array(
                'source' => $entity->getMember()->getId(),
                'amount' => $entity->getAmount(),
                'receiver' => $entity->getReceiver()->getId()
            ));
            $entity->setId($id);
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        throw new DAOException("no subsequent update");
    }


}

