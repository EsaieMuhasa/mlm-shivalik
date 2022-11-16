<?php

namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\OfficeBonus;
use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DefaultDAOInterface;

/**
 * @author Esaie MHS
 */
class VirtualMoneyDAOManagerImplementation1 extends DefaultDAOInterface implements VirtualMoneyDAOManager {

	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Dao\DAOInterface::update()
	 */
	public function update($entity, $id) : void {
		throw new DAOException("unsupported operation");
	}
	
	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Dao\DefaultDAOInterface::hasView()
	 */
	protected function hasView(): bool {
	    return true;
	}

    public function checkInputByBudget(?int $configId = null): bool
    {
        if ($configId == null) {
            $return = false;
            try {
                $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "SELECT id FROM {$this->getTableName()} WHERE config IS NULL");
                if($statement->fetch()) {
                    $return = true;
                }
                $statement->closeCursor();
            } catch (\PDOException $e) {
                throw new DAOException($e->getMessage(), $e);
            }

            return $return;
        }
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), [
            'config' => $configId
        ]);
    }

    public function findInputByBudget(?int $configId = null) : array {
        if ($configId == null) {
            $return = [];
            try {
                $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "SELECT * FROM {$this->getTableName()} WHERE config IS NULL");
                while($row = $statement->fetch()) {
                    $return[] = new VirtualMoney($row);
                }
                $statement->closeCursor();
            } catch (\PDOException $e) {
                throw new DAOException($e->getMessage(), $e);
            }
            return $return;
        }

        return UtilitaireSQL::findAll(
            $this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), 
            self::FIELD_DATE_AJOUT, true, ['config' => $configId ]
        );
    }
	
	
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
	 * @param VirtualMoney $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'product' => $entity->getProduct(),
            'config' => $entity->getConfig() != null ? $entity->getConfig()->getId() : null,
            'afiliate' => $entity->getAfiliate(),
			'office' => $entity->getOffice()->getId(),
			'request' => $entity->getRequest() != null? $entity->getRequest()->getId() : null,
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()            
        ]);
		$entity->setId($id); 
		
// 		cette operation est mise en commentaire car le dette ne doivent plus etre admisent
// 		foreach ($entity->getDebts() as $d) {
// 		    UtilitaireSQL::update($pdo, "GradeMember", [
// 		        'virtualMoney' => $id		        
// 		    ], $d->getId());
// 		}
		
		//EVOIE DU BONUS
		if ($entity->getBonus() != null) {//ssi n'est pas null
		    $this->getManagerFactory()->getManagerOf(OfficeBonus::class)->createInTransaction($entity->getBonus(), $pdo);
		}//==
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\VirtualMoneyDAOManager::findByOffice()
     */
    public function findByOffice (int $officeId) {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getViewName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, ['office' => $officeId]);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\VirtualMoneyDAOManager::checkByOffice()
     */
    public function checkByOffice (int $officeId) : bool {
        return $this->columnValueExist('office', $officeId);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\VirtualMoneyDAOManager::findByRequest()
     */
    public function findByRequest (int $requestId) : VirtualMoney {
        return UtilitaireSQL::findUnique($this->getConnection(), $this->getViewName(), $this->getMetadata()->getName(), "request", $requestId);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\VirtualMoneyDAOManager::checkByRequest()
     */
    public function checkByRequest (int $requestId) : bool {
        return $this->columnValueExist('request', $requestId);
    }
    
    
    /**
     * verification de l'historique des operations effectuer pas un office
     * @param int $officeId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public function checkCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool {
        return UtilitaireSQL::hasCreationHistory($this->getConnection(), $this->getTableName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['office' => $officeId], $limit, $offset);
    }
    
    /**
     * recuperation des l'historique des operations effectuer par un office
     * @param int $officeId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return VirtualMoney[]
     */
    public function findCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : array {
        return UtilitaireSQL::findCreationHistory($this->getConnection(), $this->getViewName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['office' => $officeId], $limit, $offset);
    }

}

