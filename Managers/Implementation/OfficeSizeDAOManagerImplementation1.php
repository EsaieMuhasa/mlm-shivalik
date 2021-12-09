<?php
namespace Managers\Implementation;

use Managers\OfficeSizeDAOManager;
use Entities\OfficeSize;
use Library\DAOException;
use Managers\OfficeDAOManager;
use Managers\SizeDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeSizeDAOManagerImplementation1 extends OfficeSizeDAOManager
{
    /**
     * @var OfficeDAOManager
     */
    private $officeDAOManager;
    
    /**
     * @var SizeDAOManager
     */
    private $sizeDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \Managers\OfficeSizeDAOManager::upgrade()
     */
    public function upgrade(OfficeSize $os): void
    {
        try {
            if ($this->pdo->beginTransaction()) {
                $this->pdo_updateInTableTransactionnel($this->pdo, $this->getTableName(), array(
                    'closeDate' => $os->getInitDate()->format('Y-m-d\T00:00:00')
                ), $os->getOld()->getId());
                $id = $this->pdo_insertInTableTansactionnel($this->pdo, $this->getTableName(), array(
                    'old' => $os->getOld()->getId(),
                    'size' => $os->getSize()->getId(),
                    'office' => $os->getOffice()->getId(),
                    'initDate' => $os->getInitDate()->format('Y-m-d\T00:00:00'),
                ));
                $os->setId($id);
            }
        } catch (\PDOException $e) {
            try {
                $this->pdo->rollBack();
            } catch (\Exception $e) {
            }
            throw new DAOException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     * @param OfficeSize $entity
     */
    public function create($entity)
    {
        try {
            if ($this->pdo->beginTransaction()) {
                $this->officeDAOManager->createInTransaction($entity->getOffice(), $this->pdo);
                
                $id = $this->pdo_insertInTableTansactionnel($this->pdo, $this->getTableName(), array(
                    'size' => $entity->getSize()->getId(),
                    'office' => $entity->getOffice()->getId(),
                    'initDate' => $entity->getInitDate()->format('Y-m-d\T00:00:00'),
                ));
                
                $entity->setId($id);
                $this->pdo->commit();
            }
        } catch (\PDOException $e) {
            try {
                $this->pdo->rollBack();
            } catch (\Exception $e) {}
            throw new DAOException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Managers\OfficeSizeDAOManager::getCurrent()
     */
    public function getCurrent($officeId): OfficeSize
    {
        
        $current = null;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE office=:office AND (initDate IS NOT NULL AND closeDate IS NULL)");
            if ($statement->execute(array('office'=>$officeId))) {
                if ($row = $statement->fetch()) {
                    $current = new OfficeSize($row);
                    $current->setSize($this->sizeDAOManager->getForId($current->getSize()->getId()));
                }else {
                    $statement->closeCursor();
                    throw new  DAOException("no result returned by the selection request");
                }
            }else {
                $statement->closeCursor();
                throw new DAOException("query execution failure");
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), );
        }
        return $current;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     * @param OfficeSize $entity
     */
    public function update($entity, $id)
    {
        throw new DAOException("impossible to perform this operation");        
    }


}

