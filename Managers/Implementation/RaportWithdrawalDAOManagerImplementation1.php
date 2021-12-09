<?php
namespace Managers\Implementation;

use Managers\RaportWithdrawalDAOManager;
use Library\DAOException;
use Entities\RaportWithdrawal;
use Library\Calendar\Month;
use Managers\OfficeDAOManager;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class RaportWithdrawalDAOManagerImplementation1 extends RaportWithdrawalDAOManager
{
    /**
     * @var OfficeDAOManager
     */
    private $officeDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \Managers\RaportWithdrawalDAOManager::canSendRaport()
     */
    public function canSendRaport(int $officeId): bool
    {
        $month = new Month();
        
        $start = $month->getFirstDay();
        $last = $month->getLastDay();
        if ($this->hasRaportInInterval($start, $last, $officeId)) {
            return false;
        }
        
        $now = new \DateTime();
        $d1 = intval($now->format('d'), 10);
        $d2 = intval($last->format('d'), 10);
        
        return (($d2 - $d1) <= 5);
    }

    /**
     * {@inheritDoc}
     * @see \Managers\RaportWithdrawalDAOManager::getRaportInInterval()
     */
    public function getRaportInInterval(\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null)
    {
        $return = array();
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE dateAjout>=:dateMin AND dateAjout<=:dateMax ".($officeId!=null? "AND office={$officeId}":""));
            if ($statement->execute(array('dateMin'  => $dateMin->format('Y-m-d\T00:00:00'), 'dateMax' => $dateMax->format('Y-m-d\T23:59:59')))) {
                
                if ($row = $statement->fetch()) {
                    $rpr = new RaportWithdrawal($row, true);
                    $rpr->setOffice($this->officeDAOManager->getForId($rpr->getOffice()->getId(), false));
                    $return[] = $rpr;
                    while ($row = $statement->fetch()) {
                        $rpr = new RaportWithdrawal($row, true);
                        $rpr->setOffice($this->officeDAOManager->getForId($rpr->getOffice()->getId(), false));
                        $return[] = $rpr;
                    }
                    $statement->closeCursor();
                } else {
                    $statement->closeCursor();
                    throw new DAOException("no report for the selection interval");
                }
            }else {
                $statement->closeCursor();
                throw new DAOException("an error occurred while executing the query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\RaportWithdrawalDAOManager::hasRaportInInterval()
     */
    public function hasRaportInInterval(\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT id FROM {$this->getTableName()} WHERE dateAjout>=:dateMin AND dateAjout<=:dateMax ".($officeId!=null? "AND office={$officeId}":""));
            if ($statement->execute(array('dateMin'  => $dateMin->format('Y-m-d\T00:00:00'), 'dateMax' => $dateMax->format('Y-m-d\T23:59:59')))) {
                
                if ($statement->fetch()) {
                    $return = true;
                }
                $statement->closeCursor();
            }else {
                $statement->closeCursor();
                throw new DAOException("an error occurred while executing the query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     */
    public function create($entity)
    {
        if (!$this->canSendRaport($entity->getOffice()->getId())) {
            throw new DAOException("cannot perform this operation the current date is not supported for the monthly report sending");
        }
        
        try {
            if ($this->pdo->beginTransaction()) {
                $this->createInTransaction($entity, $this->pdo);
                $this->pdo->commit();
            }else {
                throw new DAOException("An error occurred in the transaction");
            }
        } catch (\PDOException $e) {
            try {
                $this->pdo->rollBack();
            } catch (\Exception $e) {
            }
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::createInTransaction()
     * @param RaportWithdrawal $entity
     */
    public function createInTransaction($entity, $api): void
    {
        $id = $this->pdo_insertInTableTansactionnel($api, $this->getTableName(), array('office' => $entity->getOffice()->getId()));
        foreach ($entity->getWithdrawals() as $withd) {
            $this->pdo_updateInTableTransactionnel($api, "Withdrawal", array('raport' => $id), $withd->getId(), false);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        throw new DAOException("update operation is not supported");
    }

    
}

