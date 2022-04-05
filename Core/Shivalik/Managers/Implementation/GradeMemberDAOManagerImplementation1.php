<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\BonusGeneration;
use Core\Shivalik\Entities\Generation;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\NotificationReceiver;
use Core\Shivalik\Entities\PointValue;
use Core\Shivalik\Managers\GenerationDAOManager;
use Core\Shivalik\Managers\GradeDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class GradeMemberDAOManagerImplementation1 extends DefaultDAOInterface implements  GradeMemberDAOManager
{
    
    /**
     * @var MemberDAOManager
     */
    protected $memberDAOManager;
    
    /**
     * @var GradeDAOManager
     */
    protected $gradeDAOManager;
    
    /**
     * @var GenerationDAOManager
     */
    protected $generationDAOManager;
    
    /**
     * @var OfficeDAOManager
     */
    protected $officeDAOManager;
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::findCurrentByMember()
     */
    public function findCurrentByMember(int $memberId): GradeMember
    {
        if (!$this->checkCurrentByMember($memberId)) {
            throw new DAOException("no activation of the current account grade");
        }
        
        $current = null;
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE member=:member AND enable = 1 AND (initDate IS NOT NULL AND closeDate IS NULL)");
            if ($statement->execute(array('member'=>$memberId))) {
                if ($row = $statement->fetch()) {
                    $current = new GradeMember($row);
                    $current->setGrade($this->gradeDAOManager->findById($current->getGrade()->getId()));
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
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::findAllRequest()
     */
	public function findAllRequest() {
		$requests = array();
		try {
			$statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE initDate IS NULL AND closeDate IS NULL");
			if ($statement->execute()) {
				if ($row = $statement->fetch()) {
					$gm = new GradeMember($row);
					$this->load($gm);
					$requests[] = $gm;
					
					while ($row = $statement->fetch()) {
						$gm = new GradeMember($row);
						$this->load($gm);
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
	 * @see \Core\Shivalik\Managers\GradeMemberDAOManager::checkRequest()
	 */
	public function checkRequest(): bool {
		$requests = false;
		try {
			$statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE initDate IS NULL AND closeDate IS NULL");
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
	 * @see \Core\Shivalik\Managers\GradeMemberDAOManager::findRequestedByMember()
	 */
    public function findRequestedByMember(int $memberId): GradeMember
    {
        if (!$this->hasRequested($memberId)) {
            throw new DAOException("no current account upgrade request");
        }
        
        $requested = null;
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE member=:member AND enable = 0 AND initDate IS NULL AND closeDate IS NULL");
            if ($statement->execute(array('member'=>$memberId))) {
                if ($row = $statement->fetch()) {
                    $requested = new GradeMember($row);
                    $this->load($requested);
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
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::getOperations()
     */
	public function findByOffice(?int $officeId, ?bool $upgrade = null, ?bool $virtual=null) {
		$return = array();
		try {
			$statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE office =:office ".($upgrade === null? "":(" AND old IS ".($upgrade? "NOT":"")." NULL")).($virtual === null? "":(" AND virtualMoney IS ".($virtual? " NOT ":"")." NULL")));
			if($statement->execute(array('office' => $officeId))){
				if ($row = $statement->fetch()) {
					$ob = new GradeMember($row);
					$this->load($ob);
					$return[] = $ob;
					
					while ($row = $statement->fetch()) {
						$ob = new GradeMember($row);
						$this->load($ob);
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
	 * @see \Core\Shivalik\Managers\GradeMemberDAOManager::checkByOffice()
	 */
	public function checkByOffice(?int $officeId, ?bool $upgrade = null,  ?bool $virtual=null): bool {
		$return = false;
		try {
		    $sql = "SELECT id FROM {$this->getTableName()} WHERE office =:office ".($upgrade === null? "":(" AND old IS ".($upgrade? "NOT":"")." NULL")).($virtual === null? "":(" AND virtualMoney IS".($virtual? " NOT":"")." NULL"));
		    $statement = $this->getConnection()->prepare($sql);
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
	 * @see \Core\Shivalik\Managers\GradeMemberDAOManager::checkCurrentByMember()
	 */
    public function checkCurrentByMember(int $memberId): bool
    {
        $current = false;
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE member=:member AND enable = 1 AND (initDate IS NOT NULL) AND closeDate IS NULL");
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
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::checkRequestedByMember()
     */
    public function checkRequestedByMember(int $memberId): bool
    {
        $requested = false;
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE member=:member AND enable = 0 AND initDate IS NULL AND closeDate IS NULL");
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
     * @see \PHPBackend\Dao\DefaultDAOInterface::findById()
     */
    public function findById($id, bool $forward = true)
    {
        /**
         * @var GradeMember $gm
         */
        $gm = parent::findById($id, $forward);
        $gm->setGrade($this->gradeDAOManager->findById($gm->getGrade()->getId()));
        $gm->setMember($this->memberDAOManager->findById($gm->getMember()->getId()));
        
        if ($gm->getOld() != null) {
            $gm->setOld(UtilitaireSQL::findUnique($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_ID, $gm->getOld()->getId()));
        }
        
        return $gm;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByCreationHistory()
     */
    public function findByCreationHistory(\DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset = 0) : array
    {
        $grades = parent::findByCreationHistory($dateMin, $dateMax, $limit, $offset);
        foreach ($grades as $gm) {
            $gm->setGrade($this->gradeDAOManager->findById($gm->getGrade()->getId()));
            $gm->setMember($this->memberDAOManager->findById($gm->getMember()->getId()));
            
            if ($gm->getOld() != null) {
                $gm->setOld($this->findById($gm->getOld()->getId(), false));
            }
        }
        return $grades;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::findUpgradeHistory()
     */
    public function findUpgradeHistory(\DateTime $dateMin, \DateTime $dateMax = null, ?int $officeId=null, ?int $limit = null, int $offset = 0)
    {
        $SQL = 'SELECT * FROM '.$this->getTableName().' WHERE dateAjout >= :dateMin  AND dateAjout <=:dateMax AND old IS NOT NULL';
        
        if ($dateMax == null) {
            $dateMax = clone $dateMin;
        }

        $params = array();
        
        $params['dateMin'] = $dateMin->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $dateMax->format('Y-m-d\T23:59:59');
        
        if ($officeId !== null) {
            $SQL .= " AND office = :office";
            $params['office'] = $officeId;
        }
        
        $SQL .= (($limit !== null)? (' LIMIT '.$limit.' OFFSET '.($offset!=0? $offset:'0')):'');
        $data = array();
        
        try {
            $result = $this->getConnection()->prepare($SQL);
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
                
                $gm->setGrade($this->gradeDAOManager->findById($gm->getGrade()->getId()));
                $gm->setMember($this->memberDAOManager->findById($gm->getMember()->getId()));
                $gm->setOld($this->findById($gm->getOld()->getId(), false));
                
                while ($row= $result->fetch()) {
                    $gm =new GradeMember($row, true);
                    $data [] = $gm;
                    
                    $gm->setGrade($this->gradeDAOManager->findById($gm->getGrade()->getId()));
                    $gm->setMember($this->memberDAOManager->findById($gm->getMember()->getId()));
                    $gm->setOld($this->findById($gm->getOld()->getId(), false));
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
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::checkUpgradeHistory()
     */
    public function checkUpgradeHistory(\DateTime $dateMin, \DateTime $dateMax = null, ?int $officeId=null, ?int $limit = null, int $offset = 0): bool
    {
        $SQL = 'SELECT * FROM '.$this->getTableName().' WHERE dateAjout >= :dateMin  AND dateAjout <=:dateMax AND old IS NOT NULL';
        
        if ($dateMax == null) {
            $dateMax = clone $dateMin;
        }
        
        $params = array();
        
        $params['dateMin'] = $dateMin->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $dateMax->format('Y-m-d\T23:59:59');
        
        if ($officeId !== null) {
            $SQL .= " AND office = :office";
            $params['office'] = $officeId;
        }
        
        $SQL .= " LIMIT ".($limit === null? '1' : $limit).' OFFSET '.($offset!=0? $offset:'0');
        
        $return = false;
        try {
            $result = $this->getConnection()->prepare($SQL);
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            
            if(!$result->execute($params)){
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
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::countUpgradeHistory()
     */
    public function countUpgradeHistory(\DateTime $dateMin, \DateTime $dateMax = null, ?int $officeId = null): int
    {
        $SQL = 'SELECT COUNT(*) AS nombre FROM '.$this->getTableName().' WHERE dateAjout >= :dateMin  AND dateAjout <=:dateMax AND old IS NOT NULL';
        
        if ($dateMax == null) {
            $dateMax = clone $dateMin;
        }
        
        $params = array();
        
        $params['dateMin'] = $dateMin->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $dateMax->format('Y-m-d\T23:59:59');
        
        if ($officeId !== null) {
            $SQL .= " AND office = :office";
            $params['office'] = $officeId;
        }
        
        $return = 0;
        try {
            $result = $this->getConnection()->prepare($SQL);
            if ($result == false){
                throw new DAOException('Echec de préparation de la requête. ré-essayez svp!');
            }
            
            if(!$result->execute($params)){
                $result->closeCursor();
                throw new DAOException('Echec d\'exécution de la requête. Ré-essayez svp! ...');
            }
            
            if($row = $result->fetch()){
                $return = $row['nombre'];
            }
            $result->closeCursor();
        } catch (\Exception $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::enable()
     */
    public function enable(GradeMember $entity): void
    {
        $pdo = $this->getConnection();
        try {
            
            $notificationReceivers = [];//collection des recepteurs des notifications
            
            if (!$pdo->beginTransaction()) {
                throw new DAOException("an unknown error occurred while creating a member's rank activation transaction");
            }
            
            $sponsor = $entity->getMember()->getSponsor();
            
            if ($entity->getOld() != null) {
                //cloture des packet precedant
                UtilitaireSQL::update($pdo, $this->getTableName(), [//desactivation de l'encien statut
                    'closeDate'=> $entity->getInitDate()->format('Y-m-d\TH:i:s')                    
                ], $entity->getOld()->getId());
            }
                
            //ouverture du nouveau packet
            UtilitaireSQL::update($pdo, $this->getTableName(), [                
                'initDate'=> $entity->getInitDate()->format('Y-m-d\TH:i:s'),
                'enable' => 1
            ], $entity->getId());
            
            //envoie de la notification du proprietaire du compte
            $title = "Account package activation";
            $description = "{$entity->getMember()->getNames()} the package activation '{$entity->getGrade()->getName()}' of your account is done successfully";
            $notificationReceivers[] = NotificationReceiver::buildNotificationReceiver($title, $description, $entity->getMember());
            
            if ($sponsor != null) {
                $sponsorGrade = $this->findCurrentByMember($sponsor->getId());
                
                $amountBonusSponsor = ($entity->getProduct()/100) * $sponsorGrade->getGrade()->getPercentage();
                $amountBonusSponsor = round($amountBonusSponsor, 2, PHP_ROUND_HALF_DOWN);
                
                $bonusSponsor = new BonusGeneration(array(
                    'generator' => $entity,
                    'member' => $sponsor,
                    'amount' => $amountBonusSponsor,
                    'dateAjout' => new \DateTime()
                ));
                
                $this->getDaoManager()->getManagerOf(BonusGeneration::class)->createInTransaction($bonusSponsor, $pdo);
                
                //Envoie de la notification du sponsor du compte
                $title = "Sponsoring bonus";
                $description = " Congratulations {$entity->getMember()->getSponsor()->getNames()}. You got $ {$bonusSponsor->getAmount()} bonus, for sponsoring {$entity->getMember()->getMatricule()} account.";
                $notificationReceivers[] = NotificationReceiver::buildNotificationReceiver($title, $description, $entity->getMember()->getSponsor());
                
                /**
                 * @var Member $parent
                 */
                $parent = clone $sponsor;
                $generationNumber = Generation::MIN_GENERATION;
                
                while ($this->memberDAOManager->checkParent($parent->getId()) && $generationNumber <= Generation::MAX_GENERATION) {
                    
                    $parent = $this->memberDAOManager->findParent($parent->getId());
                    
                    $generation = $this->generationDAOManager->findByNumber($generationNumber);
                    $gradeParent = $this->findCurrentByMember($parent->getId());
                    
                    if ($gradeParent->getGrade()->getMaxGeneration()->getNumber() >= $generation->getNumber()) {
                        
                        //verification de la generation
                        $amountBonusGeneration = ($entity->getProduct()/100) * $generation->getPercentage();
                        $amountBonusGeneration = round($amountBonusGeneration, 2, PHP_ROUND_HALF_DOWN);
                        
                        $bonusGeneration = new BonusGeneration(array(
                            'generator' => $entity,
                            'member' => $parent,
                            'amount' => $amountBonusGeneration,
                            'generation' => $generation,
                            'dateAjout' => new \DateTime()
                        ));
                        
                        $this->getDaoManager()->getManagerOf(BonusGeneration::class)->createInTransaction($bonusGeneration, $pdo);
                        
                        //Envoie de la notification des uplines du compte
                        $title = "Generationnel bonus";
                        $description = "Congratulations {$parent->getNames()}. You got $ {$bonusSponsor->getAmount()} bonus, for your downline {$entity->getMember()->getMatricule()} account.";
                        $notificationReceivers[] = NotificationReceiver::buildNotificationReceiver($title, $description, $parent);
                    }
                    
                    $generationNumber++;
                }
                
            }
            
            //dispatching des PV
            
            /**
             * les PV sont appercue au pied du parent,
             * identifier par le foot du fils de sont arbre
             */
            if ($entity->getMember()->getParent() != null) {
                $child = $entity->getMember();
                while ($this->memberDAOManager->checkParent($child->getId())) {
                    $foot = $child->getFoot();
                    $child = $this->memberDAOManager->findParent($child->getId());
                    
                    $pv = new PointValue();
                    $pv->setMember($child);
                    $pv->setGenerator($entity);
                    $pv->setFoot($foot);
                    
                    $value = round(($entity->getProduct()/2), 0);
                    $pv->setValue($value);
                    $this->getDaoManager()->getManagerOf(PointValue::class)->createInTransaction($pv, $pdo);
                    
                    $title = "Generationnel bonus";
                    $description = "Congratulations {$child->getNames()}. You got  {$pv->getValue()} PV, for your downline {$entity->getMember()->getMatricule()} account, sponsorized by {$entity->getMember()->getSponsor()->getNames()}";
                    $notificationReceivers[] = NotificationReceiver::buildNotificationReceiver($title, $description, $parent);
                }
            }
            
            foreach ($notificationReceivers as $receiver) {
                $this->getDaoManager()->getManagerOf(NotificationReceiver::class)->createInTransaction($receiver, $pdo);
            }
            
            $commit = $pdo->commit();
            
            if (!$commit) {
                throw new DAOException("Transaction validation failure");
            }
            
            foreach ($notificationReceivers as $rs) {
                $event = new DAOEvent($this->getDaoManager()->getManagerOf(NotificationReceiver::class), DAOEvent::TYPE_CREATION, $rs);
                $this->dispatchEvent($event);
            }
            
        } catch (\PDOException $e) {
            try {
                $pdo->rollBack();
            } catch (\Exception $e) {
            }
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::upgrade()
     */
    public function upgrade(GradeMember $gm) : void
    {
        try {
            $pdo = $this->getConnection();
            
            if ($pdo->beginTransaction()) { 
                
                $gm->setInitDate(new \DateTime());
                
                $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [                    
                    'old' => $gm->getOld()->getId(),
                    'member' => $gm->getMember()->getId(),
                    'grade' => $gm->getGrade()->getId(),
                	'office' => $gm->getOffice()->getId(),
                    'product' => $gm->getProduct(), 
                    self::FIELD_DATE_AJOUT => $gm->getFormatedDateAjout()
                ]);
                
                $gm->setId($id);
                $pdo->commit();
                
                $this->enable($gm);
            }else{
                throw new DAOException("an error occurred while starting the transaction");
            }
            
        } catch (\PDOException $e) {
            try {
                $pdo->rollBack();
            } catch (\Exception $e) {
            }
            throw new DAOException("an error occurred during the transaction: {$e->getMessage()}");
        }
        
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::create()
     */
    public function create($entity): void
    {
        try {
            $pdo = $this->getConnection();
            if (!$pdo->beginTransaction()) {
                throw new DAOException("An errot occurred in creating transaction process");
            }
            
            $this->createInTransaction($entity, $pdo);
            if(!$pdo->commit()){
                throw new DAOException("An errors occurend in commit transaction process");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }

        $this->enable($entity);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param GradeMember $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $generator = $entity->getMember();
        if($generator->getId() == null || $generator->getId() <= 0){//on commence par creer le generateur
            $this->memberDAOManager->createInTransaction($generator, $pdo);
        }
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'member' => $entity->getMember()->getId(),
            'grade' => $entity->getGrade()->getId(),
            'product' => $entity->getProduct(),
            'membership' => $entity->getMembership(),
            'officePart' => $entity->getOfficePart(),
        	'office' => $entity->getOffice()->getId(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);

    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id) : void
    {
        throw new DAOException("Cannot perform this operation");
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::countUpgrades()
     */
    public function countUpgrades(?int $officeId = null): int
    {
        $return = 0;
        try {
            $statement = $this->getConnection()->prepare("SELECT COUNT(*) AS nombre FROM {$this->getTableName()} WHERE old IS NOT NULL ".($officeId === null? "":(" AND office=:office")));
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
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::findDebts()
     */
    public function findDebts(?int $virtualId = null)
    {
        $return = array();
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE membership > 0  AND virtual ".($virtualId == null? "IS NULL" : " = {$virtualId}"));
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
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::hasDebts()
     */
    public function hasDebts(?int $virtualId = null): bool
    {
        $return = false;
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE membership > 0  AND virtual ".($virtualId == null? "IS NULL" : " = {$virtualId}"));
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
    

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::hasUnpaid()
     */
    public function hasUnpaid (?int $officeId) : bool {
        return $this->hasOperation($officeId, null, false);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::findUnpaid()
     */
    public function findUnpaid (?int $officeId) : array {
        return $this->findOperations($officeId, null, false);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::checkCreationHistoryByOffice()
     */
    public function checkCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool {
        return UtilitaireSQL::hasCreationHistory($this->getConnection(), $this->getTableName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['office' => $officeId], $limit, $offset);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::findCreationHistoryByOffice()
     */
    public function findCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool {
        return UtilitaireSQL::findCreationHistory($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['office' => $officeId], $limit, $offset);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeMemberDAOManager::load()
     */
    public function load ($gradeMember) : GradeMember {
        $gm = ($gradeMember instanceof GradeMember)? $gradeMember : $this->findById($gradeMember);
        $gm->setOffice($this->officeDAOManager->findById($gm->getOffice()->getId(), false));
        $gm->setMember($this->memberDAOManager->findById($gm->getMember()->getId(), false));
        $gm->setGrade($this->gradeDAOManager->findById($gm->getGrade()->getId(), false));
        if ($gm->getOld() != null) {
            $gm->setOld($this->findById($gm->getOld()->getId(), false));
        }
        return $gm;
    }

  
}

