<?php
namespace Managers\Implementation;

use Managers\WithdrawalDAOManager;
use Library\DAOException;
use Entities\Withdrawal;

/**
 *
 * @author Esaie MHS
 *        
 */
class WithdrawalDAOManagerImplementation1 extends WithdrawalDAOManager
{
    
    /**
     * {@inheritDoc}
     * @see \Managers\WithdrawalDAOManager::redirect()
     */
    public function redirect(\Entities\Withdrawal $with): void
    {
        try {
            $this->pdo_updateInTable($this->getTableName(), array(
                'office' => $with->getOffice()->getId()
            ), $with->getId(), false);
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     * @param Withdrawal $entity
     */
    public function create($entity)
    {
        try {
            $id = $this->pdo_insertInTable($this->getTableName(), array(
                'member' => $entity->getMember()->getId(),
                'amount' => $entity->getAmount(),
                'office' => $entity->getOffice()->getId(),
            	'telephone' => $entity->getTelephone()
            ));
            $entity->setId($id);
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     * @param Withdrawal $entity
     */
    public function update($entity, $id)
    {
        try {
            $this->pdo_updateInTable($this->getTableName(), array(
                'office' => $entity->getOffice()->getId(),
            	'telephone' => $entity->getTelephone()
            ), $id);
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Managers\WithdrawalDAOManager::hasRequest()
     */
    public function hasRequest(int $officeId, ?bool $state = false, ?bool $sended=null): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE office=:office ".(($state !== null)? ("AND admin IS ".($state? 'NOT':'')." NULL") : ("")).(($sended !== null)? (" AND raport IS ".($sended? 'NOT':'')." NULL") : ("")));
            $statement->execute(array('office' => $officeId));
            if ($statement->fetch()) {
                $return = true;
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }
    
    /**
     * {@inheritDoc}
     * @see \Managers\WithdrawalDAOManager::getOfficeRequests()
     */
    public function getOfficeRequests(int $officeId, ?bool $state = false, ?bool $sended=null)
    {
        $return = array();
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE office={$officeId} ".(($state !== null)? ("AND admin IS ".($state? 'NOT':'')." NULL") : ("")).(($sended !== null)? (" AND raport IS ".($sended? 'NOT':'')." NULL") : ("")));
            $statement->execute();
            if ($row = $statement->fetch()) {
                $w = new Withdrawal($row);
                
                $w->setMember($this->memberDAOManager->getForId($w->getMember()->getId()));
                $w->setOffice($this->officeDAOManager->getForId($w->getOffice()->getId()));
                $return[] = $w;
                
                while ($row = $statement->fetch()) {
                    $w = new Withdrawal($row);
                    
                    $w->setMember($this->memberDAOManager->getForId($w->getMember()->getId()));
                    $w->setOffice($this->officeDAOManager->getForId($w->getOffice()->getId()));
                    $return[] = $w;
                }
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }
    
    
	/**
	 * {@inheritDoc}
	 * @see \Managers\WithdrawalDAOManager::forMember()
	 */
	public function forMember(int $memberId, int $limit = - 1, int $offset = - 1): array {
		$return = array();
		try {
			$statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE member={$memberId} ORDER BY dateAjout DESC ".(($limit!=-1 && $offset!=-1)? "LIMIT {$limit} OFFSET {$offset}" : ""));
			$statement->execute();
			if ($row = $statement->fetch()) {
				$w = new Withdrawal($row);
				
				$w->setMember($this->memberDAOManager->getForId($w->getMember()->getId()));
				$w->setOffice($this->officeDAOManager->getForId($w->getOffice()->getId()));
				$return[] = $w;
				
				while ($row = $statement->fetch()) {
					$w = new Withdrawal($row);
					
					$w->setMember($this->memberDAOManager->getForId($w->getMember()->getId()));
					$w->setOffice($this->officeDAOManager->getForId($w->getOffice()->getId()));
					$return[] = $w;
				}
			}else {
				$statement->closeCursor();
				throw new DAOException("no withdrawals for this account");
			}
			$statement->closeCursor();
		} catch (\PDOException $e) {
			throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
		}
		return $return;
	}

}

