<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\OfficeSize;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\OfficeSizeDAOManager;
use Core\Shivalik\Managers\SizeDAOManager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DefaultDAOInterface;
/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeSizeDAOManagerImplementation1 extends DefaultDAOInterface implements OfficeSizeDAOManager
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
     * @see \Core\Shivalik\Managers\OfficeSizeDAOManager::upgrade()
     */
    public function upgrade(OfficeSize $os): void
    {
        $pdo = $this->getConnection();
        try {
            if ($pdo->beginTransaction()) {
                
                UtilitaireSQL::update($pdo, $this->getTableName(), [                    
                    'closeDate' => $os->getInitDate()->format('Y-m-d\T00:00:00')
                ], $os->getOld()->getId());
                
                $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [                    
                    'old' => $os->getOld()->getId(),
                    'size' => $os->getSize()->getId(),
                    'office' => $os->getOffice()->getId(),
                    'initDate' => $os->getInitDate()->format('Y-m-d\T00:00:00'),
                    self::FIELD_DATE_AJOUT => $os->getFormatedDateAjout()
                ]);

                $os->setId($id);
                
                $pdo->commit();
                
                $event = new DAOEvent($this, DAOEvent::TYPE_CREATION, $os);
                $this->dispatchEvent($event);
            }else {
                throw new DAOException("a error has occurent in transaction process");
            }
        } catch (\PDOException $e) {
            try {
                $pdo->rollBack();
            } catch (\Exception $e) {}
            throw new DAOException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
     * @param OfficeSize $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $this->officeDAOManager->createInTransaction($entity->getOffice(), $pdo);
        
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [            
            'size' => $entity->getSize()->getId(),
            'office' => $entity->getOffice()->getId(),
            'initDate' => $entity->getInitDate()->format('Y-m-d\T00:00:00'),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);

        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeSizeDAOManager::findCurrentByOffice()
     */
    public function findCurrentByOffice(int $officeId): OfficeSize
    {
        $current = null;
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE office=:office AND (initDate IS NOT NULL AND closeDate IS NULL)");
            if ($statement->execute(array('office'=>$officeId))) {
                if ($row = $statement->fetch()) {
                    $current = new OfficeSize($row);
                    $current->setSize($this->sizeDAOManager->findById($current->getSize()->getId()));
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
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id) : void
    {
        throw new DAOException("impossible to perform this operation");        
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeSizeDAOManager::checkByOffice()
     */
    public function checkByOffice (int $officeId) : bool{
        return $this->columnValueExist("office", $officeId);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeSizeDAOManager::findByOffice()
     */
    public function findByOffice (int $officeId) : array {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, array('office' => $officeId));
    }

}

