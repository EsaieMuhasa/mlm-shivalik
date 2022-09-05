<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Managers\WithdrawalDAOManager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
class WithdrawalDAOManagerImplementation1 extends AbstractOperationDAOManager implements WithdrawalDAOManager
{
    
    /**
     * @var OfficeDAOManager
     */
    protected $officeDAOManager;
    
    /**
     * @var MemberDAOManager
     */
    protected $memberDAOManager;
    
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
     * @see \Core\Shivalik\Managers\Implementation\AbstractOperationDAOManager::update()
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
     * @see \Core\Shivalik\Managers\WithdrawalDAOManager::countByOffice()
     */
    public function countByOffice(int $officeId, ?bool $state = false, ?bool $sended = null): int
    {
        $return = 0;
        try {
            $statement = $this->getConnection()->prepare("SELECT COUNT(*) AS nombre FROM {$this->getTableName()} WHERE office=:office ".(($state !== null)? ("AND admin IS ".($state? 'NOT':'')." NULL") : ("")).(($sended !== null)? (" AND raport IS ".($sended? 'NOT':'')." NULL") : ("")).' ORDER BY dateAjout DESC');
            $statement->execute(array('office' => $officeId));
            if ($row = $statement->fetch()) {
                $return = $row['nombre'];
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\WithdrawalDAOManager::checkByOffice()
     */
    public function checkByOffice(int $officeId, ?bool $state = false, ?bool $sended=null, ?int $limit = null, int $offset = 0): bool
    {
        $return = false;
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE office=:office ".(($state !== null)? ("AND admin IS ".($state? 'NOT':'')." NULL") : ("")).(($sended !== null)? (" AND raport IS ".($sended? 'NOT':'')." NULL") : ("")).' ORDER BY dateAjout DESC'.($limit !== null? " LIMIT {$limit} OFFSET {$offset}":''));
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
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE office={$officeId} ".(($state !== null)? ("AND admin IS ".($state? 'NOT':'')." NULL") : ("")).(($sended !== null)? (" AND raport IS ".($sended? 'NOT':'')." NULL") : ("")).' ORDER BY dateAjout DESC'.($limit !== null? " LIMIT {$limit} OFFSET {$offset}":''));
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
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\WithdrawalDAOManager::validate()
     */
    public function validate (int $id, int $adminId) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('admin' => $adminId), $id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\AbstractOperationDAOManager::findByMember()
     * @return Withdrawal[]
     */
    public function findByMember(int $memberId, ?int $limit = null, int $offset = 0): array
    {
        $operations = parent::findByMember($memberId, $limit, $offset);
        foreach ($operations as $operation) {
            $operation->setOffice($this->officeDAOManager->findById($operation->office->id, false));
        }
        return $operations;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\WithdrawalDAOManager::findByRapport()
     */
    public function findByRapport(int $raportId, ?int $limit = null, int $offset = 0): array{
        /**
         * @var Withdrawal[] $raports
         */
        $raports = UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, array('raport' => $raportId), $limit, $offset);
        foreach ($raports as $raport) {
            $raport->setMember($this->memberDAOManager->findById($raport->getMember()->getId(), false));
        }
        return $raports;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\WithdrawalDAOManager::countByRapport()
     */
    public function countByRapport (int $rapportId) : int  {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), array('rapport' => $rapportId));
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByCreationHistory()
     */
    public function findByCreationHistory(\DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset = 0) : array
    {
        /**
         * @var Withdrawal[] $withs
         */
        $withs = parent::findByCreationHistory($dateMin, $dateMax, $limit, $offset);
        foreach ($withs as $withdrawel) {
            $withdrawel->setOffice($this->officeDAOManager->findById($withdrawel->office->id, false));
            $withdrawel->setMember($this->memberDAOManager->findById($withdrawel->member->id, false));
        }
        return $withs;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\WithdrawalDAOManager::checkCreationHistoryByOffice()
     */
    public function checkCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool {
        return UtilitaireSQL::hasCreationHistory($this->getConnection(), $this->getTableName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['office' => $officeId], $limit, $offset);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\WithdrawalDAOManager::findCreationHistoryByOffice()
     */
    public function findCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : array {
        return UtilitaireSQL::findCreationHistory($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['office' => $officeId], $limit, $offset);
    }

}

