<?php

namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\RequestVirtualMoney;
use Core\Shivalik\Managers\RequestVirtualMoneyDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DefaultDAOInterface;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use DateTime;

/**
 *
 * @author Esaie MHS
 *        
 */
class RequestVirtualMoneyDAOManagerImplementation1 extends DefaultDAOInterface implements RequestVirtualMoneyDAOManager {

    /**
     * @var OfficeDAOManager
     */
    protected $officeDAOManager;
    
    /**
     * @var VirtualMoneyDAOManager
     */
    protected $virtualMoneyDAOManager;
    
	/**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
	 * @param RequestVirtualMoney $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
			'office' => $entity->getOffice()->getId(),
			'amount' => $entity->getAmount(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
		$entity->setId($id);
    }

    /**
	 * {@inheritDoc}
	 * @see \PHPBackend\Dao\DAOInterface::update()
	 */
	public function update($entity, $id) : void {
		throw new DAOException("impossible to perform this operation");
	}

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\RequestVirtualMoneyDAOManager::checkRequestedInInterval()
     */
    public function checkRequestedInInterval(DateTime $dateMin, DateTime $dateMax, ?int $officeId = null): bool
    {
        $return = false;
        try {
            $statement = $this->getConnection()->prepare("SELECT id FROM {$this->getTableName()} WHERE dateAjout>=:dateMin AND dateAjout<=:dateMax ".($officeId!=null? "AND office={$officeId}":""));
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
     * @see \Core\Shivalik\Managers\RequestVirtualMoneyDAOManager::findRequestedInInterval()
     */
    public function findRequestedInInterval(DateTime $dateMin, DateTime $dateMax, ?int $officeId = null): array
    {
        $return = array();
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE dateAjout>=:dateMin AND dateAjout<=:dateMax ".($officeId!=null? "AND office={$officeId}":""));
            if ($statement->execute(array('dateMin'  => $dateMin->format('Y-m-d\T00:00:00'), 'dateMax' => $dateMax->format('Y-m-d\T23:59:59')))) {
                
                if ($row = $statement->fetch()) {
                    $rpr = new RequestVirtualMoney($row, true);
                    $rpr->setOffice($this->officeDAOManager->findById($rpr->getOffice()->getId(), false));
                    $return[] = $rpr;
                    while ($row = $statement->fetch()) {
                        $rpr = new RequestVirtualMoney($row, true);
                        $rpr->setOffice($this->officeDAOManager->findById($rpr->getOffice()->getId(), false));
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
     * @see \Core\Shivalik\Managers\RequestVirtualMoneyDAOManager::findWaiting()
     */
    public function findWaiting(?int $officeId = null)
    {
        $FIELDS = "{$this->getTableName()}.id AS id, {$this->getTableName()}.office AS office, {$this->getTableName()}.dateAjout AS dateAjout, {$this->getTableName()}.dateModif AS dateModif, {$this->getTableName()}.deleted AS deleted, {$this->getTableName()}.amount AS amount";
        $SQL = "SELECT {$FIELDS}  FROM {$this->getTableName()} LEFT JOIN VirtualMoney ON  {$this->getTableName()}.id = VirtualMoney.request WHERE VirtualMoney.request IS NULL ".($officeId!=null? "AND {$this->getTableName()}.office={$officeId}":"");
        $return = array();
        try {
            $statementt = $this->getConnection()->prepare($SQL);
            if ($statementt->execute()) {
                if ($row = $statementt->fetch()) {
                    $request = new RequestVirtualMoney($row, true);
                    $request->setOffice($this->officeDAOManager->findById($request->getOffice()->getId(), false));
                    $return[] = $request;
                    while ($row = $statementt->fetch()) {
                        $request = new RequestVirtualMoney($row, true);
                        $request->setOffice($this->officeDAOManager->findById($request->getOffice()->getId(), false));
                        $return[] = $request;
                    }
                }else {
                    $statementt->closeCursor();
                    throw new DAOException("No result return by selection request query");
                }
                $statementt->closeCursor();
            }else {
                $statementt->closeCursor();
                throw new DAOException("Failure execution query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\RequestVirtualMoneyDAOManager::checkWaiting()
     */
    public function checkWaiting(?int $officeId = null): bool
    {
        $SQL = "SELECT {$this->getTableName()}.id FROM {$this->getTableName()} LEFT JOIN VirtualMoney ON  {$this->getTableName()}.id = VirtualMoney.request WHERE VirtualMoney.request IS NULL ".($officeId!=null? "AND {$this->getTableName()}.office={$officeId}":"");
        $return = false;
        try {
            $statementt = $this->getConnection()->prepare($SQL);
            if ($statementt->execute()) {
                if ($statementt->fetch()) {
                    $return = true;
                }
                $statementt->closeCursor();
            }else {
                $statementt->closeCursor();
                throw new DAOException("Failure execution query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByColumnName()
     */
    public function findByColumnName(string $columnName, $value, bool $forward = true)
    {
        /**
         * @var RequestVirtualMoney $request
         */
        $request = parent::findByColumnName($columnName, $value, $forward);
        $request->setOffice($this->officeDAOManager->findById($request->office->id, false));
        return $request;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\RequestVirtualMoneyDAOManager::checkByOffice()
     */
    public function checkByOffice (int $officeId) : bool{
        return  $this->columnValueExist('office', $officeId);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\RequestVirtualMoneyDAOManager::findByOffice()
     */
    public function findByOffice (int $officeId) {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, array("office" => $officeId));
    }
    


}

