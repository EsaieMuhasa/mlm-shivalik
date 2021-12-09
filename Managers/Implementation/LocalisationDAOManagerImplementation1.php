<?php
namespace Managers\Implementation;

use Managers\LocalisationDAOManager;
use Entities\Localisation;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
class LocalisationDAOManagerImplementation1 extends LocalisationDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::getForId()
     * @return Localisation
     */
    public function getForId(int $id, bool $forward = true)
    {
        /**
         * @var Localisation $localisation
         */
        $localisation = parent::getForId($id, $forward);
        $localisation->setCountry($this->getDaoManager()->getManagerOf('Country')->getForId($localisation->getCountry()->getId()));
        return $localisation;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     * @param Localisation $entity
     */
    public function create($entity)
    {
        try {
            if ($this->pdo->beginTransaction()) {
                $this->createInTransaction($entity, $this->pdo);
                $this->pdo->commit();
            }else {
                throw new DAOException("An error occurred while creating the transaction");
            }                ;
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
        try {
            if ($this->pdo->beginTransaction()) {
                $this->updateInTransaction($entity, $id, $this->pdo);
                $this->pdo->commit();
            }else {
                throw new DAOException("An error occurred while creating the transaction");
            }                ;
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::createInTransaction()
     */
    public function createInTransaction($entity, $api): void
    {
        $id=$this->pdo_insertInTableTansactionnel($api, $this->getTableName(), array(
            'country' => $entity->getCountry()->getId(),
            'city' => $entity->getCity(),
            'district' => $entity->getDistrict()
        ));
        $entity->setId($id);

    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::updateInTransaction()
     */
    public function updateInTransaction($entity, ?int $id, $api): void
    {

        $this->pdo_updateInTableTransactionnel($api, $this->getTableName(), array(
            'country' => $entity->getCountry()->getId(),
            'city' => $entity->getCity(),
            'district' => $entity->getDistrict()
        ), $id);
        
    }



}

