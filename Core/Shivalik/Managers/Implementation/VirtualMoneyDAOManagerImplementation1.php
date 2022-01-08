<?php

namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Entities\OfficeBonus;

/**
 *
 * @author Esaie MHS
 *        
 */
class VirtualMoneyDAOManagerImplementation1 extends VirtualMoneyDAOManager {

	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Dao\DAOInterface::update()
	 */
	public function update($entity, $id) {
		throw new DAOException("unsupported operation");
	}
	
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
	 * @param VirtualMoney $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
			'amount' => $entity->getAmount(),
		    'expected' => $entity->getExpected(),
		    'request' => $entity->getRequest()!=null? $entity->getRequest()->getId() : null,
			'office' => $entity->getOffice()->getId(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()            
        ]);
		$entity->setId($id); 
		
		foreach ($entity->getDebts() as $d) {
		    UtilitaireSQL::update($pdo, "GradeMember", [
		        'virtualMoney' => $id		        
		    ], $d->getId());
		}
		
		//EVOIE DU BONUS
		if ($entity->getBonus()->getAmount()>0) {//ssi suppieur a zero
		    $this->getDaoManager()->getManagerOf(OfficeBonus::class)->createInTransaction($entity->getBonus(), $pdo);
		}
    }

}

