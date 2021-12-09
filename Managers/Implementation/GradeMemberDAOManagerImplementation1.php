<?php
namespace Managers\Implementation;

use Managers\GradeMemberDAOManager;
use Entities\GradeMember;
use Entities\Generation;
use Entities\BonusGeneration;
use Library\DAOException;
use Entities\PointValue;

/**
 *
 * @author Esaie MHS
 *        
 */
class GradeMemberDAOManagerImplementation1 extends GradeMemberDAOManager
{
    
    /**
     * {@inheritDoc}
     * @see \Managers\GradeMemberDAOManager::getCurrent()
     */
    public function getCurrent(int $memberId): GradeMember
    {
        if (!$this->hasCurrent($memberId)) {
            throw new DAOException("no activation of the current account grade");
        }
        
        $current = null;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE member=:member AND enable = 1 AND (initDate IS NOT NULL AND closeDate IS NULL)");
            if ($statement->execute(array('member'=>$memberId))) {
                if ($row = $statement->fetch()) {
                    $current = new GradeMember($row);
                    $current->setGrade($this->gradeDAOManager->getForId($current->getGrade()->getId()));
                }else {
                    $statement->closeCursor();
                    throw new  DAOException("no result returned by the selection request");
                }
            }else {
                $statement->closeCursor();
                throw new DAOException("query execution failure");
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), );
        }
        return $current;
    }

    /**
	 * {@inheritDoc}
	 * @see \Managers\GradeMemberDAOManager::getAllRequest()
	 */
	public function getAllRequest() {
		$requests = array();
		try {
			$statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE initDate IS NULL AND closeDate IS NULL");
			if ($statement->execute()) {
				if ($row = $statement->fetch()) {
					$gm = new GradeMember($row);
					$gm->setOffice($this->officeDAOManager->getForId($gm->getOffice()->getId(), false));
					$gm->setMember($this->memberDAOManager->getForId($gm->getMember()->getId()));
					$gm->setGrade($this->gradeDAOManager->getForId($gm->getGrade()->getId(), false));
					
					if ($gm->getOld() != null) {
						$gm->setOld($this->getForId($gm->getOld()->getId(), false));
					}
					
					$requests[] = $gm;
					
					while ($row = $statement->fetch()) {
						$gm = new GradeMember($row);
						$gm->setOffice($this->officeDAOManager->getForId($gm->getOffice()->getId(), false));
						$gm->setMember($this->memberDAOManager->getForId($gm->getMember()->getId()));
						$gm->setGrade($this->gradeDAOManager->getForId($gm->getGrade()->getId(), false));
						
						if ($gm->getOld() != null) {
							$gm->setOld($this->getForId($gm->getOld()->getId(), false));
						}
						
						$requests[] = $gm;
					}
					
					$statement->closeCursor();
				} else {
					$statement->closeCursor();
					throw new DAOException("no pending request");
				}
			}else {
				throw new DAOException("an error occurred while executing query");
			}
		} catch (\PDOException $e) {
			throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
		}
		return $requests;
	}

	/**
	 * {@inheritDoc}
	 * @see \Managers\GradeMemberDAOManager::hasRequest()
	 */
	public function hasRequest(): bool {
		$requests = false;
		try {
			$statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE initDate IS NULL AND closeDate IS NULL");
			if ($statement->execute()) {
				if ($statement->fetch()) {
					$requests = true;
				}
				$statement->closeCursor();
			} else {
				$statement->closeCursor();
				throw new DAOException("an error occurred while executing query");
			}
		} catch (\PDOException $e) {
			throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
		}
		return $requests;
	}

	/**
     * {@inheritDoc}
     * @see \Managers\GradeMemberDAOManager::getRequested()
     */
    public function getRequested(int $memberId): GradeMember
    {
        if (!$this->hasRequested($memberId)) {
            throw new DAOException("no current account upgrade request");
        }
        
        $requested = null;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE member=:member AND enable = 0 AND initDate IS NULL AND closeDate IS NULL");
            if ($statement->execute(array('member'=>$memberId))) {
                if ($row = $statement->fetch()) {
                    $requested = new GradeMember($row);
                    $requested->setGrade($this->gradeDAOManager->getForId($requested->getGrade()->getId()));
                }else {
                    $statement->closeCursor();
                    throw new  DAOException("no result returned by the selection request");
                }
            }else {
                $statement->closeCursor();
                throw new DAOException("query execution failure");
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), );
        }
        return $requested;
    }

    /**
	 * {@inheritDoc}
	 * @see \Managers\GradeMemberDAOManager::getOperations()
	 */
	public function getOperations(?int $officeId, ?bool $upgrade = null, ?bool $virtual=null) {
		$return = array();
		try {
			$statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE office =:office ".($upgrade === null? "":(" AND old IS ".($upgrade? "NOT":"")." NULL")).($virtual === null? "":(" AND virtualMoney IS ".($virtual? " NOT ":"")." NULL")));
			if($statement->execute(array('office' => $officeId))){
				if ($row = $statement->fetch()) {
					$ob = new GradeMember($row);
					
					$ob->setMember($this->memberDAOManager->getForId($ob->getMember()->getId()));
					$ob->setGrade($this->gradeDAOManager->getForId($ob->getGrade()->getId()));
					
					if ($ob->getOld() != null) {
						$ob->setOld($this->getForId($ob->getOld()->getId()));
					}
					
					$return[] = $ob;
					
					while ($row = $statement->fetch()) {
						$ob = new GradeMember($row);
						
						$ob->setMember($this->memberDAOManager->getForId($ob->getMember()->getId()));
						$ob->setGrade($this->gradeDAOManager->getForId($ob->getGrade()->getId()));
						
						if ($ob->getOld() != null) {
							$ob->setOld($this->getForId($ob->getOld()->getId()));
						}
						
						$return[] = $ob;
					}
				}else {
					$statement->closeCursor();
					throw new DAOException("No operations in database for this office");
				}
				
				$statement->closeCursor();
			}else {
				$statement->closeCursor();
				throw new DAOException("an indefined error occurred while executing the query");
			}
		} catch (\PDOException $e) {
			throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
		}
		return $return;
	}

	/**
	 * {@inheritDoc}
	 * @see \Managers\GradeMemberDAOManager::hasOperation()
	 */
	public function hasOperation(?int $officeId, ?bool $upgrade = null,  ?bool $virtual=null): bool {
		$return = false;
		try {
		    $sql = "SELECT id FROM {$this->getTableName()} WHERE office =:office ".($upgrade === null? "":(" AND old IS ".($upgrade? "NOT":"")." NULL")).($virtual === null? "":(" AND virtualMoney IS".($virtual? " NOT":"")." NULL"));
		    //die($sql);
		    $statement = $this->pdo->prepare($sql);
			if($statement->execute(array('office' => $officeId))){
				if ($statement->fetch()) {
					$return = true;
				}
				
				$statement->closeCursor();
			}else {
				$statement->closeCursor();
				throw new DAOException("an indefined error occurred while executing the query");
			}
		} catch (\PDOException $e) {
			throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
		}
		return $return;
	}

	/**
     * {@inheritDoc}
     * @see \Managers\GradeMemberDAOManager::hasCurrent()
     */
    public function hasCurrent(int $memberId): bool
    {
        $current = false;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE member=:member AND enable = 1 AND (initDate IS NOT NULL) AND closeDate IS NULL");
            if ($statement->execute(array('member'=>$memberId))) {
                if ($statement->fetch()) {
                    $current = true;
                }
            }else {
                $statement->closeCursor();
                throw new DAOException("query execution failure");
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), );
        }
        return $current;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\GradeMemberDAOManager::hasRequested()
     */
    public function hasRequested(int $memberId): bool
    {
        $requested = false;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE member=:member AND enable = 0 AND initDate IS NULL AND closeDate IS NULL");
            if ($statement->execute(array('member'=>$memberId))) {
                if ($statement->fetch()) {
                    $requested = true;
                }
            }else {
                $statement->closeCursor();
                throw new DAOException("query execution failure");
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), );
        }
        return $requested;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::getForId()
     */
    public function getForId(int $id, bool $forward = true)
    {
        /**
         * @var GradeMember $gm
         */
        $gm = parent::getForId($id, $forward);
        $gm->setGrade($this->gradeDAOManager->getForId($gm->getGrade()->getId()));
        $gm->setMember($this->memberDAOManager->getForId($gm->getMember()->getId()));
        
        if ($gm->getOld() != null) {
            $gm->setOld($this->pdo_uniqueFromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'id', $gm->getOld()->getId()));
        }
        
        return $gm;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::getCreationHistory()
     */
    public function getCreationHistory(\DateTime $dateMin, \DateTime $dateMax = null, array $filters = array(), $limit = - 1, $offset = - 1)
    {
        $grades = parent::getCreationHistory($dateMin, $dateMax, $filters, $limit, $offset);
        foreach ($grades as $gm) {
            $gm->setGrade($this->gradeDAOManager->getForId($gm->getGrade()->getId()));
            $gm->setMember($this->memberDAOManager->getForId($gm->getMember()->getId()));
            
            if ($gm->getOld() != null) {
                $gm->setOld($this->getForId($gm->getOld()->getId(), false));
            }
        }
        return $grades;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\GradeMemberDAOManager::getUpgradeHistory()
     */
    public function getUpgradeHistory(\DateTime $dateMin, \DateTime $dateMax = null, array $filters = array(), $limit = - 1, $offset = - 1)
    {
        $SQL = 'SELECT * FROM '.$this->getTableName().' WHERE dateAjout >= :dateMin  AND dateAjout <=:dateMax AND old IS NOT NULL';
        
        if ($dateMax == null) {
            $dateMax = clone $dateMin;
        }

        $params = array();
        
        $params['dateMin'] = $dateMin->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $dateMax->format('Y-m-d\T23:59:59');
        
        if (!empty($filters)) {
            $i = 0;
            $count = count($filters);
            $SQL .= " AND ";
            
            foreach (array_keys($filters) as $name) {
                $SQL .= "{$name}=:{$name}".($i < ($count-1)? ' AND ':'');
                $params[$name] = $filters[$name];
                $i++;
            }
        }
        
        $SQL .= ' AND deleted=0 '.(($limit>-1 && $offset>-1)? (' LIMIT '.$limit.' OFFSET '.($offset!=0? $offset:'0')):'');
        $data = array();
        try {
            $result = $this->pdo->prepare($SQL);
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            $status = $result->execute($params);
            
            if(!$status){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            
            if($row=$result->fetch()){
                $gm =new GradeMember($row, true);
                $data [] = $gm;
                //die($row['expected']);
                
                $gm->setGrade($this->gradeDAOManager->getForId($gm->getGrade()->getId()));
                $gm->setMember($this->memberDAOManager->getForId($gm->getMember()->getId()));
                $gm->setOld($this->getForId($gm->getOld()->getId(), false));
                
                while ($row= $result->fetch()) {
                    $gm =new GradeMember($row, true);
                    $data [] = $gm;
                    
                    $gm->setGrade($this->gradeDAOManager->getForId($gm->getGrade()->getId()));
                    $gm->setMember($this->memberDAOManager->getForId($gm->getMember()->getId()));
                    $gm->setOld($this->getForId($gm->getOld()->getId(), false));
                }
                
                $result->closeCursor();
            }else {
                $result->closeCursor();
                throw new DAOException('Aucun résultat en déhors des critères de restriction "'.$this->getTableName().'"');
            }
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $data;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\GradeMemberDAOManager::hasUpgradeHistory()
     */
    public function hasUpgradeHistory(\DateTime $dateMin, \DateTime $dateMax = null, array $filters = array(), $limit = - 1, $offset = - 1): bool
    {
        $SQL = 'SELECT * FROM '.$this->getTableName().' WHERE (dateAjout >= :dateMin  AND dateAjout <=:dateMax) AND old IS NOT NULL ';
        
        if ($dateMax == null) {
            $dateMax = clone $dateMin;
        }
        
        $params = array();
        
        $params['dateMin'] = $dateMin->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $dateMax->format('Y-m-d\T23:59:59');
        
        if (!empty($filters)) {
            $i = 0;
            $count = count($filters);
            $SQL .= " AND ";
            foreach (array_keys($filters) as $name) {
                $SQL .= "{$name}=:{$name}".($i < ($count-1)? ' AND ':'');
                $params[$name] = $filters[$name];
                $i++;
            }
        }
        
        $SQL .= ' AND deleted=0 '.(($limit>-1 && $offset>-1)? (' LIMIT '.$limit.' OFFSET '.($offset!=0? $offset:'0')):'');
        $return = false;
        try {
            $result = $this->pdo->prepare($SQL);
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            $status = $result->execute($params);
            
            if(!$status){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            if($result->fetch()){
                $return = true;
            }
            $result->closeCursor();
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\GradeMemberDAOManager::enable()
     */
    public function enable(GradeMember $entity): void
    {
        try {
            
            if (!$this->pdo->beginTransaction()) {
                throw new DAOException("an unknown error occurred while creating a member's rank activation transaction");
            }
            
            $sponsor = $entity->getMember()->getSponsor();
            
            if ($entity->getOld() != null) {
                //cloture des packet precedant
                $this->pdo_updateInTableTransactionnel($this->pdo, $this->getTableName(), array(//desactivation de l'encien statut
                    'closeDate'=> $entity->getInitDate()->format('Y-m-d\TH:i:s')
                ), $entity->getOld()->getId());
            }
                
            //ouverture du nouveau packet
            $this->pdo_updateInTableTransactionnel($this->pdo, $this->getTableName(), array(//activation du nouveau statut
                'initDate'=> $entity->getInitDate()->format('Y-m-d\TH:i:s'),
                'enable' => 1
            ), $entity->getId());
            
            if ($sponsor != null) {
                $sponsorGrade = $this->getCurrent($sponsor->getId());
                
                $amountBonusSponsor = ($entity->getProduct()/100) * $sponsorGrade->getGrade()->getPercentage();
                $amountBonusSponsor = round($amountBonusSponsor, 2, PHP_ROUND_HALF_DOWN);
                
                $bonusSponsor = new BonusGeneration(array(
                    'generator' => $entity,
                    'member' => $sponsor,
                    'amount' => $amountBonusSponsor
                ));
                
                $this->getDaoManager()->getManagerOf('BonusGeneration')->createInTransaction($bonusSponsor, $this->pdo);
                
                /**
                 * @var \Entities\Member $parent
                 */
                $parent = clone $sponsor;
                $generationNumber = Generation::MIN_GENERATION;
                
                while ($this->memberDAOManager->hasParent($parent->getId()) && $generationNumber <= Generation::MAX_GENERATION) {
                    
                    $parent = $this->memberDAOManager->getParent($parent->getId());
                    
                    $generation = $this->generationDAOManager->forNumber($generationNumber);
                    $gradeParent = $this->getCurrent($parent->getId());
                    
                    if ($gradeParent->getGrade()->getMaxGeneration()->getNumber() >= $generation->getNumber()) {
                        
                        //verification de la generation
                        $amountBonusGeneration = ($entity->getProduct()/100) * $generation->getPercentage();
                        $amountBonusGeneration = round($amountBonusGeneration, 2, PHP_ROUND_HALF_DOWN);
                        
                        $bonusGeneration = new BonusGeneration(array(
                            'generator' => $entity,
                            'member' => $parent,
                            'amount' => $amountBonusGeneration,
                            'generation' => $generation
                        ));
                        
                        $this->getDaoManager()->getManagerOf('BonusGeneration')->createInTransaction($bonusGeneration, $this->pdo);
                    }
                    
                    $generationNumber++;
                }
                
            }
            
            //dispaching des PV
            
            /**
             * les PV sont appercue au pied du parent,
             * identifier par le foot du fils de sont arbre
             */
            if ($entity->getMember()->getParent()!=null) {
                $member = $entity->getMember();
                $child = clone $member;
                while ($this->memberDAOManager->hasParent($member->getId())) {
                    $foot = $child->getFoot();
                    
                    $member = $this->memberDAOManager->getParent($member->getId());
                    $child = clone $member;
                    
                    $pv = new PointValue();
                    $pv->setMember($member);
                    $pv->setGenerator($entity);
                    $pv->setFoot($foot);
                    
                    $value = round(($entity->getProduct()/2), 0);
                    $pv->setValue($value);
                    $this->getDaoManager()->getManagerOf('PointValue')->createInTransaction($pv, $this->pdo);
                }
            }
            
            $commit = $this->pdo->commit();
            if (!$commit) {
                throw new DAOException("Transaction validation failure");
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
     * @see \Managers\GradeMemberDAOManager::upgrade()
     */
    public function upgrade(GradeMember $gm) : void
    {
        try {
            if ($this->pdo->beginTransaction()) {                
                $id = $this->pdo_insertInTableTansactionnel($this->pdo, $this->getTableName(), array(
                    'old' => $gm->getOld()->getId(),
                    'member' => $gm->getMember()->getId(),
                    'grade' => $gm->getGrade()->getId(),
                	'office' => $gm->getOffice()->getId(),
                    'product' => $gm->getProduct()
                ));
                $gm->setId($id);
                $gm->setInitDate(new \DateTime());
                $this->pdo->commit();
                $this->enable($gm);
            }else{
                throw new DAOException("an error occurred while starting the transaction");
            }
            
        } catch (\PDOException $e) {
            try {
                $this->pdo->rollBack();
            } catch (\Exception $e) {
            }
            throw new DAOException("an error occurred during the transaction: {$e->getMessage()}");
        }
        
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     */
    public function create($entity)
    {
        try {
            if ($this->pdo->beginTransaction()) {
                $this->createInTransaction($entity, $this->pdo);
                $this->pdo->commit();
                $this->enable($entity);
            }else{
                throw new DAOException("an error occurred while starting the transaction");
            }
        } catch (\PDOException $e) {
            try {
                $this->pdo->rollBack();
            } catch (\Exception $e) {
            }
            throw new DAOException("an error occurred during the transaction: {$e->getMessage()}");
        }
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::createInTransaction()
     * @param GradeMember $entity
     */
    public function createInTransaction($entity, $api): void
    {
        //on commence par creer le generateur
        
        $generator = $entity->getMember();
        
        $this->memberDAOManager->createInTransaction($generator, $api);
        $id = $this->pdo_insertInTableTansactionnel($api, $this->getTableName(), array(
            'member' => $entity->getMember()->getId(),
            'grade' => $entity->getGrade()->getId(),
            'product' => $entity->getProduct(),
            'membership' => $entity->getMembership(),
            'officePart' => $entity->getOfficePart(),
        	'office' => $entity->getOffice()->getId()
        ));
        $entity->setId($id);

    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        throw new DAOException("Cannot perform this operation");
    }
    
    /**
     * {@inheritDoc}
     * @see \Managers\GradeMemberDAOManager::countUpgrades()
     */
    public function countUpgrades(?int $officeId = null): int
    {
        $return = 0;
        try {
            $statement = $this->pdo->prepare("SELECT COUNT(*) AS nombre FROM {$this->getTableName()} WHERE old IS NOT NULL ".($officeId === null? "":(" AND office=:office")));
            if($statement->execute(array('office' => $officeId))){
                if ($row = $statement->fetch()) {
                    $return = intval($row['nombre']);
                }
                $statement->closeCursor();
            }else {
                $statement->closeCursor();
                throw new DAOException("an indefined error occurred while executing the query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }
    
    /**
     * {@inheritDoc}
     * @see \Managers\GradeMemberDAOManager::getDebts()
     */
    public function getDebts(?int $virtualId = null)
    {
        $return = array();
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE membership > 0  AND virtual ".($virtualId == null? "IS NULL" : " = {$virtualId}"));
            if($statement->execute(array())){
                if ($row = $statement->fetch()) {
                    $return[] = new GradeMember($row);
                    while ($row=$statement->fetch()) {
                        $return[] = new GradeMember($row);
                    }
                } else {
                    $statement->closeCursor();
                    throw new DAOException("on data returned by selection query");
                }
                
                $statement->closeCursor();
            }else {
                $statement->closeCursor();
                throw new DAOException("an indefined error occurred while executing the query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\GradeMemberDAOManager::hasDebts()
     */
    public function hasDebts(?int $virtualId = null): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE membership > 0  AND virtual ".($virtualId == null? "IS NULL" : " = {$virtualId}"));
            if($statement->execute(array())){
                if ($statement->fetch()) {
                    $return = true;
                }
                
                $statement->closeCursor();
            }else {
                $statement->closeCursor();
                throw new DAOException("an indefined error occurred while executing the query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

  
}

