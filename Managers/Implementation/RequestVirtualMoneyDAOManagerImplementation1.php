<?php

namespace Managers\Implementation;

use Managers\RequestVirtualMoneyDAOManager;
use Library\DAOException;
use Entities\RequestVirtualMoney;

/**
 *
 * @author Esaie MHS
 *        
 */
class RequestVirtualMoneyDAOManagerImplementation1 extends RequestVirtualMoneyDAOManager {
	
	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractDAOManager::create()
	 * @param RequestVirtualMoney $entity
	 */
	public function create($entity) {
		$id = $this->pdo_insertInTable($this->getTableName(), array(
			'office' => $entity->getOffice()->getId(),
			'amount' => $entity->getAmount()
		));
		$entity->setId($id);
	}

	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractDAOManager::update()
	 */
	public function update($entity, $id) {
		throw new DAOException("impossible to perform this operation");
	}
	
    /**
     * {@inheritDoc}
     * @see \Managers\RequestVirtualMoneyDAOManager::getWaiting()
     */
    public function getWaiting(?int $officeId = null)
    {
        $FIELDS = "{$this->getTableName()}.id AS id, {$this->getTableName()}.office AS office, {$this->getTableName()}.dateAjout AS dateAjout, {$this->getTableName()}.dateModif AS dateModif, {$this->getTableName()}.deleted AS deleted, {$this->getTableName()}.amount AS amount";
        $SQL = "SELECT {$FIELDS}  FROM {$this->getTableName()} LEFT JOIN VirtualMoney ON  {$this->getTableName()}.id = VirtualMoney.request WHERE VirtualMoney.request IS NULL ".($officeId!=null? "AND {$this->getTableName()}.office={$officeId}":"");
        $return = array();
        try {
            $statementt = $this->pdo->prepare($SQL);
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
     * @see \Managers\RequestVirtualMoneyDAOManager::hasWaiting()
     */
    public function hasWaiting(?int $officeId = null): bool
    {
        $SQL = "SELECT {$this->getTableName()}.id FROM {$this->getTableName()} LEFT JOIN VirtualMoney ON  {$this->getTableName()}.id = VirtualMoney.request WHERE VirtualMoney.request IS NULL ".($officeId!=null? "AND {$this->getTableName()}.office={$officeId}":"");
        $return = false;
        try {
            $statementt = $this->pdo->prepare($SQL);
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

