<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\RaportWithdrawal;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\RaportWithdrawalDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Calendar\Month;
use PHPBackend\Dao\UtilitaireSQL;

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
     * @see \Core\Shivalik\Managers\RaportWithdrawalDAOManager::canSendRaport()
     */
    public function canSendRaport(int $officeId): bool
    {
        $month = new Month();
        
        $start = $month->getFirstDay();
        $last = $month->getLastDay();
        if ($this->checkRaportInInterval($start, $last, $officeId)) {
            return false;
        }
        
        $now = new \DateTime();
        $d1 = intval($now->format('d'), 10);
        $d2 = intval($last->format('d'), 10);
        
        return (($d2 - $d1) <= 5);
    }

    /**S
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\RaportWithdrawalDAOManager::findRaportInInterval()
     */
    public function findRaportInInterval(\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null)
    {
        $return = array();
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE dateAjout>=:dateMin AND dateAjout<=:dateMax ".($officeId!=null? "AND office={$officeId}":""));
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
     * @see \Core\Shivalik\Managers\RaportWithdrawalDAOManager::checkRaportInInterval()
     */
    public function checkRaportInInterval(\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null): bool
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
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param RaportWithdrawal $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'office' => $entity->getOffice()->getId()
        ]);
        
        foreach ($entity->getWithdrawals() as $withd) {
            UtilitaireSQL::update($pdo, "Withdrawal", ['raport' => $id], $withd->getId());
        }
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id) : void
    {
        throw new DAOException("update operation is not supported");
    }

    
}

