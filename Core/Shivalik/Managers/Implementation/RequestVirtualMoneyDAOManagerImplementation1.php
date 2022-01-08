<?php

namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\RequestVirtualMoney;
use Core\Shivalik\Managers\RequestVirtualMoneyDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class RequestVirtualMoneyDAOManagerImplementation1 extends RequestVirtualMoneyDAOManager {


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
                    $request->setOffice($this->officeDAOManager->getForId($request->getOffice()->getId(), false));
                    $return[] = $request;
                    while ($row = $statementt->fetch()) {
                        $request = new RequestVirtualMoney($row, true);
                        $request->setOffice($this->officeDAOManager->getForId($request->getOffice()->getId(), false));
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


}

