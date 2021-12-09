<?php

namespace Managers\Implementation;

use Managers\VirtualMoneyDAOManager;
use Entities\VirtualMoney;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
class VirtualMoneyDAOManagerImplementation1 extends VirtualMoneyDAOManager {
	
	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractDAOManager::create()
	 * @param VirtualMoney $entity
	 */
	public function create($entity) {
	    try {
    	    if ($this->pdo->beginTransaction()) {
    	        $this->createInTransaction($entity, $this->pdo);
    	        $this->pdo->commit();
    	    }else{
    	        throw new DAOException("an error occurred while creating the transaction");
    	    }
	    } catch (\PDOException $e) {
	        try {
	            $this->pdo->rollBack();
	        } catch (\Exception $e) {}
	        throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
	    }

	}

	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractDAOManager::update()
	 */
	public function update($entity, $id) {
		throw new DAOException("unsupported operation");
	}
	
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::createInTransaction()
     * @param VirtualMoney $entity
     */
    public function createInTransaction($entity, $api): void
    {
        $id = $this->pdo_insertInTableTansactionnel($this->pdo, $this->getTableName(), array(    	            
			'amount' => $entity->getAmount(),
		    'expected' => $entity->getExpected(),
		    'request' => $entity->getRequest()!=null? $entity->getRequest()->getId() : null,
			'office' => $entity->getOffice()->getId()
        ));
		$entity->setId($id); 
		
		foreach ($entity->getDebts() as $d) {
		    $this->pdo_updateInTableTransactionnel($api, "GradeMember", array(
		        'virtualMoney' => $id
		    ), $d->getId(), false);
		}
		
		//EVOIE DU BONUS
		if ($entity->getBonus()->getAmount()>0) {//ssi suppieur a zero
		    $this->getDaoManager()->getManagerOf("OfficeBonus")->createInTransaction($entity->getBonus(), $api);
		}
    }

}

