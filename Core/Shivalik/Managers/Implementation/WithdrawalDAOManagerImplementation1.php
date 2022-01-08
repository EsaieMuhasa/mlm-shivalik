<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Managers\WithdrawalDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DAOEvent;

/**
 *
 * @author Esaie MHS
 *        
 */
class WithdrawalDAOManagerImplementation1 extends WithdrawalDAOManager
{
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\WithdrawalDAOManager::redirect()
     */
    public function redirect(Withdrawal $with): void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'office' => $with->getOffice()->getId(),
            self::FIELD_DATE_MODIF => $with->getFormatedDateModif()
        ], $with->getId());
        $event = new DAOEvent($this, DAOEvent::TYPE_UPDATION, $with);
        $this->dispatchEvent($event);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Withdrawal $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'member' => $entity->getMember()->getId(),
            'amount' => $entity->getAmount(),
            'office' => $entity->getOffice()->getId(),
        	'telephone' => $entity->getTelephone(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AbstractOperationDAOManager::update()
     * @param Withdrawal $entity
     */
    public function update($entity, $id) : void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'office' => $entity->getOffice()->getId(),
        	'telephone' => $entity->getTelephone()
        ], $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\WithdrawalDAOManager::checkByOffice()
     */
    public function checkByOffice(int $officeId, ?bool $state = false, ?bool $sended=null, ?int $limit = null, int $offset = 0): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE office=:office ".(($state !== null)? ("AND admin IS ".($state? 'NOT':'')." NULL") : ("")).(($sended !== null)? (" AND raport IS ".($sended? 'NOT':'')." NULL") : ("")));
            $statement->execute(array('office' => $officeId));
            if ($statement->fetch()) {
                $return = true;
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\WithdrawalDAOManager::findByOffice()
     */
    public function findByOffice(int $officeId, ?bool $state = false, ?bool $sended=null, ?int $limit = null, int $offset = 0) : array
    {
        $return = array();
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE office={$officeId} ".(($state !== null)? ("AND admin IS ".($state? 'NOT':'')." NULL") : ("")).(($sended !== null)? (" AND raport IS ".($sended? 'NOT':'')." NULL") : ("")));
            $statement->execute();
            if ($row = $statement->fetch()) {
                $w = new Withdrawal($row);
                
                $w->setMember($this->memberDAOManager->findById($w->getMember()->getId()));
                $w->setOffice($this->officeDAOManager->findById($w->getOffice()->getId()));
                $return[] = $w;
                
                while ($row = $statement->fetch()) {
                    $w = new Withdrawal($row);
                    
                    $w->setMember($this->memberDAOManager->findById($w->getMember()->getId()));
                    $w->setOffice($this->officeDAOManager->findById($w->getOffice()->getId()));
                    $return[] = $w;
                }
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

}

