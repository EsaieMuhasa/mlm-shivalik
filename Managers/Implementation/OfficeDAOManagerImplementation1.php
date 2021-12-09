<?php
namespace Managers\Implementation;

use Managers\OfficeDAOManager;
use Entities\Office;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeDAOManagerImplementation1 extends OfficeDAOManager
{

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     * @param Office $entity
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
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        try {
            if ($this->pdo->beginTransaction()) {
                $this->updateInTransaction($entity, $id, $this->pdo);
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
     * @param Office $entity
     */
    public function createInTransaction($entity, $api): void
    {
        if ($entity->getLocalisation() != null) {
            $this->localisationDAOManager->createInTransaction($entity->getLocalisation(), $api);
        }
        
        $id = $this->pdo_insertInTableTansactionnel($api, $this->getTableName(), array(
            'name' => $entity->getName(),
            'photo' => $entity->getPhoto(),
        	'member' => $entity->getMember()->getId(),
            'localisation' => ($entity->getLocalisation() != null? $entity->getLocalisation()->getId() : null)
        ));
        
        $entity->setId($id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::updateInTransaction()
     * @param Office $entity
     */
    public function updateInTransaction($entity, ?int $id, $api): void
    {
        $data = array('name' => $entity->getName());
        
        if ($entity->getPhoto() != null) {
            $data['photo'] = $entity->getPhoto();
        }
        
        $this->pdo_updateInTableTransactionnel($api, $this->getTableName(), $data, $id);
    }


}

